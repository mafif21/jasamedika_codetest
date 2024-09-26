<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\Rent\StoreRentCarRequest;
use App\Models\Car;
use App\Models\Rent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $data = Rent::paginate($perPage);
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

    public function store(StoreRentCarRequest $request)
    {
        DB::beginTransaction();
        try {
            $car = Car::findOrFail($request->car_id);
            if (!$this->isCarAvailable($request->car_id, $request->start_date, $request->end_date)) {
                return ApiResponseClass::sendResponse(null, 'Car is not available for the selected dates', 400);
            }

            $rent = Rent::create($request->validated());
            $car->update(['is_available' => false]);

            DB::commit();
            return ApiResponseClass::sendResponse($rent, 'success create new rent', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $rent = Rent::with(['user:id,name,address,phone,email', 'car:id,brand,model,plate_number,price_rate,is_available'])
                ->select('id', 'user_id', 'car_id', 'start_date', 'end_date')
                ->findOrFail($id);
            return ApiResponseClass::sendResponse($rent, 'success get car detail', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseClass::sendResponse(null, 'Rent not found', 404);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }


    private function isCarAvailable($carId, $startDate, $endDate)
    {
        return !Rent::where('car_id', $carId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }
}