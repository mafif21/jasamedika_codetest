<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\car\StoreCarRequest;
use App\Http\Requests\Car\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Interfaces\CarRepositoryInterface;
use App\Models\Car;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $query = Car::query();
            if ($request->has('brand')) {
                $query->where('brand', 'like', '%' . $request->get('brand') . '%');
            }
            if ($request->has('model')) {
                $query->where('model', 'like', '%' . $request->get('model') . '%');
            }
            if ($request->has('is_available')) {
                $query->where('is_available', $request->get('is_available'));
            }

            $data = $query->paginate($perPage);
            $paginationData = [
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
            ];
            return ApiResponseClass::sendResponse($paginationData, 'success get all data', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreCarRequest $request)
    {
        try {
            $car = Car::create($request->validated());
            return ApiResponseClass::sendResponse($car, 'success create new car', 201);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $car = Car::findOrFail($id);
            return ApiResponseClass::sendResponse($car, 'success get car detail', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseClass::sendResponse(null, 'Car not found', 404);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }

    public function update(UpdateCarRequest $request, $id)
    {
        try {
            $car = Car::findOrFail($id);

            $car->update($request->validated());
            return ApiResponseClass::sendResponse($car, 'Car updated successfully', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseClass::sendResponse(null, 'Car not found', 404);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $car = Car::findOrFail($id);
            Car::destroy($car->id);

            return ApiResponseClass::sendResponse(null, 'success delete existing car', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseClass::sendResponse(null, 'Car not found', 404);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }
}