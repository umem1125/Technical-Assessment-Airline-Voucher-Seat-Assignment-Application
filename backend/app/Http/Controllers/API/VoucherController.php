<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckVoucherRequest;
use App\Http\Requests\GenerateVoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use App\Services\SeatGeneratorService;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    protected $seatGenerator;

    public function __construct(SeatGeneratorService $seatGenerator)
    {
        $this->seatGenerator = $seatGenerator;
    }

    public function check(CheckVoucherRequest $request): JsonResponse
    {
        $exists = Voucher::query()
            ->where('flight_number', $request->flightNumber)
            ->where('flight_date', $request->daate)
            ->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }

    public function generate(GenerateVoucherRequest $request)
    {
        $exists = Voucher::query()
            ->where('flight_number', $request->flightNumber)
            ->where('flight_date', $request->date)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Vouchers already have been generated for this flight and date.',
            ], 422);
        }

        $seats = $this->seatGenerator->generateSeats($request->aircraft);

        $voucher = Voucher::create([
            'crew_name' => $request->name,
            'crew_id' => $request->id,
            'flight_number' => $request->flightNumber,
            'flight_date' => $request->date,
            'aircraft_type' => $request->aircraft,
            'seat1' => $seats[0],
            'seat2' => $seats[1],
            'seat3' => $seats[2],
        ]);

        return new VoucherResource($voucher);
    }
}
