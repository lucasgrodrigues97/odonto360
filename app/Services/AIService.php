<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Dentist;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $openaiApiKey;

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key');
    }

    /**
     * Suggest optimal appointment times using AI
     */
    public function suggestAppointmentTimes($dentist, $preferredDates, $duration = 60)
    {
        try {
            // Get dentist's historical data
            $historicalData = $this->getDentistHistoricalData($dentist);

            // Get available slots for each preferred date
            $availableSlots = [];
            foreach ($preferredDates as $date) {
                $slots = $this->getAvailableTimeSlots($dentist, $date, $duration);
                if (! empty($slots)) {
                    $availableSlots[$date] = $slots;
                }
            }

            if (empty($availableSlots)) {
                return [
                    'suggestions' => [],
                    'reasoning' => 'Nenhum horário disponível nas datas preferidas',
                ];
            }

            // Use AI to analyze and suggest optimal times
            $aiSuggestions = $this->getAISuggestions($dentist, $availableSlots, $historicalData, $duration);

            return [
                'suggestions' => $aiSuggestions,
                'reasoning' => 'Sugestões baseadas em padrões históricos e disponibilidade',
                'available_slots' => $availableSlots,
            ];

        } catch (\Exception $e) {
            Log::error('Erro no serviço de IA: '.$e->getMessage());

            // Fallback to simple suggestions
            return $this->getFallbackSuggestions($availableSlots);
        }
    }

    /**
     * Get dentist's historical appointment data
     */
    private function getDentistHistoricalData($dentist)
    {
        $last30Days = now()->subDays(30);

        $appointments = Appointment::where('dentist_id', $dentist->id)
            ->where('appointment_date', '>=', $last30Days)
            ->where('status', '!=', 'cancelled')
            ->get();

        $data = [
            'total_appointments' => $appointments->count(),
            'cancellation_rate' => $appointments->where('status', 'cancelled')->count() / max($appointments->count(), 1),
            'average_duration' => $appointments->avg('duration') ?? 60,
            'busy_hours' => $this->getBusyHours($appointments),
            'busy_days' => $this->getBusyDays($appointments),
            'patient_preferences' => $this->getPatientPreferences($appointments),
        ];

        return $data;
    }

    /**
     * Get busy hours from historical data
     */
    private function getBusyHours($appointments)
    {
        $hourCounts = [];
        foreach ($appointments as $appointment) {
            $hour = date('H', strtotime($appointment->appointment_time));
            $hourCounts[$hour] = ($hourCounts[$hour] ?? 0) + 1;
        }

        arsort($hourCounts);

        return array_slice($hourCounts, 0, 5, true);
    }

    /**
     * Get busy days from historical data
     */
    private function getBusyDays($appointments)
    {
        $dayCounts = [];
        foreach ($appointments as $appointment) {
            $dayOfWeek = date('N', strtotime($appointment->appointment_date));
            $dayCounts[$dayOfWeek] = ($dayCounts[$dayOfWeek] ?? 0) + 1;
        }

        arsort($dayCounts);

        return $dayCounts;
    }

    /**
     * Get patient preferences from historical data
     */
    private function getPatientPreferences($appointments)
    {
        $preferences = [
            'morning_preference' => 0,
            'afternoon_preference' => 0,
            'evening_preference' => 0,
        ];

        foreach ($appointments as $appointment) {
            $hour = (int) date('H', strtotime($appointment->appointment_time));

            if ($hour >= 8 && $hour < 12) {
                $preferences['morning_preference']++;
            } elseif ($hour >= 12 && $hour < 18) {
                $preferences['afternoon_preference']++;
            } else {
                $preferences['evening_preference']++;
            }
        }

        return $preferences;
    }

    /**
     * Get AI suggestions using OpenAI API
     */
    private function getAISuggestions($dentist, $availableSlots, $historicalData, $duration)
    {
        if (! $this->openaiApiKey) {
            return $this->getFallbackSuggestions($availableSlots);
        }

        try {
            $prompt = $this->buildPrompt($dentist, $availableSlots, $historicalData, $duration);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um assistente especializado em otimização de agendamentos odontológicos. Analise os dados fornecidos e sugira os melhores horários para agendamento, considerando padrões históricos, preferências dos pacientes e disponibilidade do dentista.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['choices'][0]['message']['content'] ?? '';

                return $this->parseAIResponse($aiResponse, $availableSlots);
            }

        } catch (\Exception $e) {
            Log::error('Erro na API do OpenAI: '.$e->getMessage());
        }

        return $this->getFallbackSuggestions($availableSlots);
    }

    /**
     * Analyze appointment patterns using AI
     */
    public function analyzeAppointmentPatterns($dentistId, $days = 30)
    {
        try {
            $appointments = Appointment::where('dentist_id', $dentistId)
                ->where('appointment_date', '>=', now()->subDays($days))
                ->where('status', '!=', 'cancelled')
                ->get();

            if ($appointments->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Dados insuficientes para análise',
                ];
            }

            $analysisData = $this->prepareAnalysisData($appointments);

            if (! $this->openaiApiKey) {
                return $this->getBasicAnalysis($analysisData);
            }

            $prompt = $this->buildAnalysisPrompt($analysisData);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um analista especializado em dados de agendamentos odontológicos. Analise os padrões fornecidos e forneça insights sobre otimização de horários, preferências dos pacientes e sugestões de melhoria.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 1500,
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiAnalysis = $result['choices'][0]['message']['content'] ?? '';

                return [
                    'success' => true,
                    'data' => [
                        'analysis' => $aiAnalysis,
                        'patterns' => $analysisData,
                        'recommendations' => $this->extractRecommendations($aiAnalysis),
                    ],
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erro na análise de padrões: '.$e->getMessage());
        }

        return $this->getBasicAnalysis($analysisData);
    }

    /**
     * Predict optimal scheduling times using AI
     */
    public function predictOptimalTimes($dentistId, $date, $duration = 60)
    {
        try {
            $historicalData = $this->getDentistHistoricalData(
                Dentist::findOrFail($dentistId)
            );

            $availableSlots = $this->getAvailableTimeSlots(
                Dentist::findOrFail($dentistId),
                $date,
                $duration
            );

            if (empty($availableSlots)) {
                return [
                    'success' => false,
                    'message' => 'Nenhum horário disponível para a data especificada',
                ];
            }

            if (! $this->openaiApiKey) {
                return [
                    'success' => true,
                    'data' => $this->getFallbackSuggestions([$date => $availableSlots]),
                ];
            }

            $prompt = $this->buildPredictionPrompt($dentistId, $date, $availableSlots, $historicalData, $duration);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->openaiApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um especialista em otimização de agendamentos. Com base nos dados históricos e padrões, preveja os melhores horários para agendamento, considerando fatores como demanda, preferências dos pacientes e eficiência do dentista.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.6,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $prediction = $result['choices'][0]['message']['content'] ?? '';

                return [
                    'success' => true,
                    'data' => [
                        'predictions' => $this->parsePredictionResponse($prediction, $availableSlots),
                        'confidence' => $this->calculateConfidence($historicalData),
                        'reasoning' => $prediction,
                    ],
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erro na previsão de horários: '.$e->getMessage());
        }

        return [
            'success' => true,
            'data' => $this->getFallbackSuggestions([$date => $availableSlots]),
        ];
    }

    /**
     * Build prompt for AI
     */
    private function buildPrompt($dentist, $availableSlots, $historicalData, $duration)
    {
        $prompt = "Analise os seguintes dados para sugerir os melhores horários de agendamento:\n\n";

        $prompt .= "Dentista: {$dentist->user->name}\n";
        $prompt .= "Especialização: {$dentist->specialization}\n";
        $prompt .= "Duração da consulta: {$duration} minutos\n\n";

        $prompt .= "Dados históricos (últimos 30 dias):\n";
        $prompt .= "- Total de consultas: {$historicalData['total_appointments']}\n";
        $prompt .= '- Taxa de cancelamento: '.round($historicalData['cancellation_rate'] * 100, 1)."%\n";
        $prompt .= "- Duração média: {$historicalData['average_duration']} minutos\n";

        $prompt .= "\nHorários mais movimentados:\n";
        foreach ($historicalData['busy_hours'] as $hour => $count) {
            $prompt .= "- {$hour}:00 - {$count} consultas\n";
        }

        $prompt .= "\nPreferências dos pacientes:\n";
        $prefs = $historicalData['patient_preferences'];
        $total = array_sum($prefs);
        if ($total > 0) {
            $prompt .= '- Manhã: '.round(($prefs['morning_preference'] / $total) * 100, 1)."%\n";
            $prompt .= '- Tarde: '.round(($prefs['afternoon_preference'] / $total) * 100, 1)."%\n";
            $prompt .= '- Noite: '.round(($prefs['evening_preference'] / $total) * 100, 1)."%\n";
        }

        $prompt .= "\nHorários disponíveis:\n";
        foreach ($availableSlots as $date => $slots) {
            $prompt .= "\nData: {$date}\n";
            foreach (array_slice($slots, 0, 10) as $slot) { // Limit to first 10 slots
                $prompt .= "- {$slot['time']}\n";
            }
        }

        $prompt .= "\nCom base nesses dados, sugira os 5 melhores horários para agendamento, considerando:\n";
        $prompt .= "1. Preferências históricas dos pacientes\n";
        $prompt .= "2. Horários menos movimentados\n";
        $prompt .= "3. Disponibilidade do dentista\n";
        $prompt .= "4. Padrões de cancelamento\n\n";
        $prompt .= 'Responda no formato JSON com uma lista de sugestões, cada uma contendo: data, hora, score (0-100), reasoning (explicação).';

        return $prompt;
    }

    /**
     * Parse AI response
     */
    private function parseAIResponse($aiResponse, $availableSlots)
    {
        try {
            // Try to extract JSON from response
            if (preg_match('/\[.*\]/s', $aiResponse, $matches)) {
                $suggestions = json_decode($matches[0], true);

                if (is_array($suggestions)) {
                    return array_slice($suggestions, 0, 5);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao processar resposta da IA: '.$e->getMessage());
        }

        return $this->getFallbackSuggestions($availableSlots);
    }

    /**
     * Get fallback suggestions when AI is not available
     */
    private function getFallbackSuggestions($availableSlots)
    {
        $suggestions = [];
        $score = 100;

        foreach ($availableSlots as $date => $slots) {
            foreach (array_slice($slots, 0, 3) as $slot) { // Take first 3 slots from each date
                $suggestions[] = [
                    'date' => $date,
                    'time' => $slot['time'],
                    'score' => $score,
                    'reasoning' => 'Horário disponível baseado na agenda do dentista',
                ];
                $score -= 10;
            }
        }

        return array_slice($suggestions, 0, 5);
    }

    /**
     * Get available time slots for a dentist
     */
    private function getAvailableTimeSlots($dentist, $date, $duration)
    {
        $dayOfWeek = date('N', strtotime($date));

        if (! in_array($dayOfWeek, $dentist->available_days ?? [])) {
            return [];
        }

        $startTime = $dentist->available_hours_start;
        $endTime = $dentist->available_hours_end;

        $slots = [];
        $currentTime = strtotime($startTime);
        $endTimestamp = strtotime($endTime);

        while ($currentTime < $endTimestamp) {
            $timeSlot = date('H:i', $currentTime);

            $isAvailable = ! Appointment::where('dentist_id', $dentist->id)
                ->where('appointment_date', $date)
                ->where('appointment_time', $timeSlot)
                ->whereIn('status', [Appointment::STATUS_SCHEDULED, Appointment::STATUS_CONFIRMED])
                ->exists();

            if ($isAvailable) {
                $slots[] = [
                    'time' => $timeSlot,
                    'formatted_time' => date('H:i', $currentTime),
                    'available' => true,
                ];
            }

            $currentTime += $duration * 60;
        }

        return $slots;
    }

    /**
     * Prepare analysis data for AI
     */
    private function prepareAnalysisData($appointments)
    {
        $data = [
            'total_appointments' => $appointments->count(),
            'by_hour' => [],
            'by_day' => [],
            'by_procedure' => [],
            'cancellation_rate' => 0,
            'average_duration' => 0,
        ];

        foreach ($appointments as $appointment) {
            $hour = $appointment->appointment_time->format('H');
            $day = $appointment->appointment_date->format('l');

            $data['by_hour'][$hour] = ($data['by_hour'][$hour] ?? 0) + 1;
            $data['by_day'][$day] = ($data['by_day'][$day] ?? 0) + 1;

            if ($appointment->procedures) {
                foreach ($appointment->procedures as $procedure) {
                    $data['by_procedure'][$procedure->name] =
                        ($data['by_procedure'][$procedure->name] ?? 0) + 1;
                }
            }
        }

        return $data;
    }

    /**
     * Build analysis prompt for AI
     */
    private function buildAnalysisPrompt($analysisData)
    {
        return "Analise os seguintes dados de agendamentos odontológicos dos últimos 30 dias:

Total de agendamentos: {$analysisData['total_appointments']}
Distribuição por horário: ".json_encode($analysisData['by_hour']).'
Distribuição por dia da semana: '.json_encode($analysisData['by_day']).'
Procedimentos mais comuns: '.json_encode($analysisData['by_procedure']).'

Forneça insights sobre:
1. Melhores horários para agendamento
2. Padrões de demanda por dia da semana
3. Procedimentos mais populares
4. Sugestões para otimização da agenda
5. Recomendações para redução de cancelamentos';
    }

    /**
     * Build prediction prompt for AI
     */
    private function buildPredictionPrompt($dentistId, $date, $availableSlots, $historicalData, $duration)
    {
        return "Com base nos dados históricos do dentista ID {$dentistId} para a data {$date}:

Dados históricos: ".json_encode($historicalData).'
Horários disponíveis: '.json_encode($availableSlots)."
Duração do procedimento: {$duration} minutos

Preveja os melhores horários para agendamento considerando:
1. Padrões históricos de demanda
2. Preferências dos pacientes
3. Eficiência do dentista
4. Horários de maior sucesso

Forneça uma lista priorizada dos melhores horários com justificativa.";
    }

    /**
     * Get basic analysis without AI
     */
    private function getBasicAnalysis($analysisData)
    {
        $mostPopularHour = array_keys($analysisData['by_hour'], max($analysisData['by_hour']))[0] ?? '09:00';
        $mostPopularDay = array_keys($analysisData['by_day'], max($analysisData['by_day']))[0] ?? 'Monday';

        return [
            'success' => true,
            'data' => [
                'analysis' => "Análise básica: Horário mais popular às {$mostPopularHour}, dia mais popular: {$mostPopularDay}",
                'patterns' => $analysisData,
                'recommendations' => [
                    "Foque nos horários das {$mostPopularHour}",
                    "Aumente disponibilidade nas {$mostPopularDay}s",
                    'Monitore padrões de cancelamento',
                ],
            ],
        ];
    }

    /**
     * Extract recommendations from AI analysis
     */
    private function extractRecommendations($aiAnalysis)
    {
        // Implementar extração de recomendações da análise da IA
        return [
            'Análise de IA disponível',
            'Considere os padrões identificados',
            'Implemente as sugestões fornecidas',
        ];
    }

    /**
     * Parse prediction response from AI
     */
    private function parsePredictionResponse($prediction, $availableSlots)
    {
        // Implementar parsing da resposta de previsão
        return $this->getFallbackSuggestions($availableSlots);
    }

    /**
     * Calculate confidence score
     */
    private function calculateConfidence($historicalData)
    {
        $dataPoints = count($historicalData);

        return min(95, max(60, $dataPoints * 2)); // 60-95% baseado na quantidade de dados
    }
}
