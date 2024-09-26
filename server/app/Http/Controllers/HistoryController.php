<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\History\StoreHistoryRequest;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $data = History::paginate($perPage);
            $paginationData = [
                'data' => $data->items(),
                'current_page' => $data->currentPage(),
                'from' => $data->firstItem(),
                'last_page' => $data->lastPage(),
                'total' => $data->total(),
            ];
            return ApiResponseClass::sendResponse($paginationData, 'success get all data return', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
        }
    }

    //     public function returnCar(StoreHistoryRequest $request)
    //     {
    //         DB::beginTransaction();
    //         try {
    //             $car = Car::findOrFail($request->car_id);
    //             if (!$car) {
    //                 return ApiResponseClass::sendResponse(null, 'Car not found', 404);
    //             }

    //             if (!$this->isCarAvailable($request->car_id, $request->start_date, $request->end_date)) {
    //                 return ApiResponseClass::sendResponse(null, 'Car is not available for the selected dates', 400);
    //             }

    //             $history = History::create($request->validated());
    //             $car->update(['is_available' => false]);

    //             DB::commit();
    //             return ApiResponseClass::sendResponse($history, 'success create new rent', 201);
    //         } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //             return ApiResponseClass::sendResponse(null, 'Car not found', 404);
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             return ApiResponseClass::sendResponse(null, 'Error: ' . $e->getMessage(), 500);
    //         }
    //     }
}
