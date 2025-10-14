<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecializationController extends Controller
{
    /**
     * Get all specializations
     */
    public function index(Request $request)
    {
        $query = Specialization::query();

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $specializations = $query->orderBy('name')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $specializations,
        ]);
    }

    /**
     * Get specialization details
     */
    public function show($id)
    {
        $specialization = Specialization::with('dentists.user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $specialization,
        ]);
    }

    /**
     * Create new specialization (admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:specializations',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $specialization = Specialization::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Especialização criada com sucesso',
            'data' => $specialization,
        ], 201);
    }

    /**
     * Update specialization (admin only)
     */
    public function update(Request $request, $id)
    {
        $specialization = Specialization::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:specializations,name,'.$id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        $specialization->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Especialização atualizada com sucesso',
            'data' => $specialization,
        ]);
    }

    /**
     * Delete specialization (admin only)
     */
    public function destroy($id)
    {
        $specialization = Specialization::findOrFail($id);

        // Check if specialization is being used by dentists
        if ($specialization->dentists()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir especialização que está sendo usada por dentistas',
            ], 422);
        }

        $specialization->delete();

        return response()->json([
            'success' => true,
            'message' => 'Especialização excluída com sucesso',
        ]);
    }

    /**
     * Get all specializations (for dropdowns)
     */
    public function all()
    {
        $specializations = Specialization::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $specializations,
        ]);
    }
}
