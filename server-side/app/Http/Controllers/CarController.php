<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\CreateUpdateCarRequest;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $filters = $request->only(['brand', 'model', 'is_available']);

            $query = Car::query();

            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $query->when($key === 'brand' || $key === 'model', function ($q) use ($key, $value) {
                        return $q->where($key, 'like', "%{$value}%");
                    });

                    $query->when($key === 'is_available', function ($q) use ($value) {
                        return $q->where('is_available', $value);
                    });
                }
            }

            $data = $query->paginate($perPage);

            return ApiResponseClass::sendResponse([
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
            ], 'success get all data', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function store(CreateUpdateCarRequest $request)
    {
        try {
            $car = Car::create($request->validated());
            return ApiResponseClass::sendResponse($car, 'success create new car', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function show(Car $car)
    {
        try {
            return ApiResponseClass::sendResponse($car, 'success get car detail', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function update(CreateUpdateCarRequest $request, Car $car)
    {
        try {
            $car->update($request->validated());
            return ApiResponseClass::sendResponse($car, 'car updated successfully', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Car $car)
    {
        try {
            $car->delete();
            return ApiResponseClass::sendResponse(null, 'success delete existing car', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }
}
