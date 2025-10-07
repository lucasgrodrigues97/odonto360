<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcedureController extends Controller
{
    /**
     * Get all procedures
     */
    public function index(Request $request)
    {
        $query = Procedure::query();

        // Search by name or code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $procedures = $query->orderBy('name')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $procedures
        ]);
    }

    /**
     * Get procedure details
     */
    public function show($id)
    {
        $procedure = Procedure::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $procedure
        ]);
    }

    /**
     * Create new procedure (admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'code' => 'required|string|max:20|unique:procedures',
            'price' => 'required|numeric|min:0|max:9999.99',
            'duration' => 'required|integer|min:15|max:480',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $procedure = Procedure::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Procedimento criado com sucesso',
            'data' => $procedure
        ], 201);
    }

    /**
     * Update procedure (admin only)
     */
    public function update(Request $request, $id)
    {
        $procedure = Procedure::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'code' => 'sometimes|string|max:20|unique:procedures,code,' . $id,
            'price' => 'sometimes|numeric|min:0|max:9999.99',
            'duration' => 'sometimes|integer|min:15|max:480',
            'category' => 'sometimes|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $procedure->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Procedimento atualizado com sucesso',
            'data' => $procedure
        ]);
    }

    /**
     * Delete procedure (admin only)
     */
    public function destroy($id)
    {
        $procedure = Procedure::findOrFail($id);

        // Check if procedure is being used in appointments
        if ($procedure->appointments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir procedimento que está sendo usado em agendamentos'
            ], 422);
        }

        $procedure->delete();

        return response()->json([
            'success' => true,
            'message' => 'Procedimento excluído com sucesso'
        ]);
    }

    /**
     * Get procedures by category
     */
    public function byCategory($category)
    {
        $procedures = Procedure::where('category', $category)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $procedures
        ]);
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        $categories = Procedure::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get all procedures (for dropdowns)
     */
    public function all()
    {
        $procedures = Procedure::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'price', 'duration', 'category']);

        return response()->json([
            'success' => true,
            'data' => $procedures
        ]);
    }
}
