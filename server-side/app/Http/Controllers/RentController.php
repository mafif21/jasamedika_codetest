<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreRentRequest;
use App\Models\Car;
use App\Models\Rent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $data = Rent::paginate($perPage);

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

    public function store(StoreRentRequest $request){
        $authenticatedUser = Auth::user();
        $request->user_id = $authenticatedUser->id;

        DB::beginTransaction();
        try {
            $foundCar = Car::findOrFail($request->get('car_id'));
            if (!$this->isCarAvailable($request->car_id, $request->start_date, $request->end_date)) {
                return ApiResponseClass::sendResponse(null, 'car is not available', 400);
            }

            $rent = Rent::create($request->validated());
            $foundCar->update(['is_available' => false]);

            DB::commit();
            return ApiResponseClass::sendResponse($rent, 'success rent car', 201);

        }catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    private function isCarAvailable($carId, $startDate, $endDate)
    {
        return !Rent::where('car_id', $carId)->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }

    public function show(Rent $rent)
    {
        try {
            $rentDetails = $rent->load(['user:id,name,address,phone,email', 'car:id,brand,model,plate_number,price_rate,is_available']);
            return ApiResponseClass::sendResponse($rentDetails, 'success get detail rent', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Rent $rent)
    {
        try {
            $rent->delete();
            return ApiResponseClass::sendResponse(null, 'success delete existing rent', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'error: ' . $e->getMessage(), 500);
        }
    }
}
