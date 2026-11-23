<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    /**
     * Display a listing of couriers.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'level' => ['nullable', 'string'],
            'sort' => ['nullable', 'in:name,created_at'],
            'direction' => ['nullable', 'in:asc,desc'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Courier::query();

        // Search by name, supporting multiple keywords.
        if (!empty($validated['search'])) {
            $keywords = preg_split(
                '/\s+/',
                trim($validated['search']),
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            foreach ($keywords as $keyword) {
                $query->whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%' . strtolower($keyword) . '%']
                );
            }
        }

        // Filter by courier levels.
        if (!empty($validated['level'])) {
            $levels = explode(',', $validated['level']);

            Validator::make(
                ['levels' => $levels],
                [
                    'levels' => ['array'],
                    'levels.*' => ['required', 'integer', 'between:1,5'],
                ]
            )->validate();

            $query->whereIn('level', $levels);
        }

        // Sorting.
        $sort = $validated['sort'] ?? 'name';
        $direction = $validated['direction'] ?? 'asc';

        $query->orderBy($sort, $direction);

        // Pagination.
        $couriers = $query->paginate(
            $validated['per_page'] ?? 10
        );

        return response()->json($couriers);
    }

    /**
     * Display the specified courier.
     */
    public function show(Courier $courier): JsonResponse
    {
        return response()->json([
            'data' => $courier,
        ]);
    }

    /**
     * Store a newly created courier.
     */
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return response()->json([
            'message' => 'Courier created successfully.',
            'data' => $courier,
        ], 201);
    }

    /**
     * Update the specified courier.
     */
    public function update(
        UpdateCourierRequest $request,
        Courier $courier
    ): JsonResponse {
        $courier->update($request->validated());

        return response()->json([
            'message' => 'Courier updated successfully.',
            'data' => $courier->fresh(),
        ]);
    }

    /**
     * Remove the specified courier.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json([
            'message' => 'Courier deleted successfully.',
        ]);
    }
}
