<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreRentRequest;
use App\Http\Requests\StoreReturnRentRequest;
use App\Models\Car;
use App\Models\Rent;
use App\Models\ReturnRent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;

class ReturnRentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $data = ReturnRent::paginate($perPage);

            return ApiResponseClass::sendResponse([
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
            ], 'success get all data return', 200);

        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function show(ReturnRent $returnRent)
    {
        try {
            return ApiResponseClass::sendResponse($returnRent, 'success get return car detail', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreReturnRentRequest $request){
        try {
            $foundRent = Rent::findOrFail($request->get('rent_id'));
            $foundCar = Car::where('plate_number', $request->get('plate_number'))->firstOrFail();
            $userId = 1;

            if ($foundRent->user_id !== $userId){
                return ApiResponseClass::sendResponse(null, 'user not valid', 400);
            }

            $startDate = Carbon::parse($foundRent->start_date);
            $endDate = Carbon::parse($foundRent->end_date);
            $totalDays = $startDate->diffInDays($endDate);

            $totalCost = $foundCar->price_rate * $totalDays;

            $validatedData = array_merge($request->validated(), [
                'total_days' => $totalDays,
                'total_cost' => $totalCost,
            ]);

            DB::beginTransaction();
            $returnRent = ReturnRent::create($validatedData);
            $foundCar->update(['is_available' => true]);

            DB::commit();
            return ApiResponseClass::sendResponse($returnRent, 'success return car', 201);

        }catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }
}
