<?php

namespace App\Services;

use InvalidArgumentException;

class SeatGeneratorService
{
    public function generateSeats(string $aircraftType): array
    {
        $layout = $this->getAircraftLayout($aircraftType);
        $allSeats = [];

        // generate valid seat
        for ($row = $layout['min_row']; $row <= $layout['max_row']; $row++) { 
            foreach ($layout['seats'] as $seatLetter) {
                $allSeats[] = $row . $seatLetter;
            }
        }

        shuffle($allSeats);

        return array_slice($allSeats, 0, 3);
    }

    private function getAircraftLayout(string $aircraftType): array
    {
        return match($aircraftType) {
            'ATR' => [
                'min_row' => 1,
                'max_row' => 18,
                'seats' => ['A', 'C', 'D', 'F']
            ],
            'Airbus 320', 'Boeing 737 Max' => [
                'min_row' => 1,
                'max_row' => 18,
                'seats' => ['A', 'B', 'C', 'D', 'E', 'F']
            ],
            default => throw new InvalidArgumentException("Tipe pesawat tidak dikenal."),
        };
    }
}
