<?php
declare(strict_types=1);
namespace Intercom;

class DistanceCalculator
{
    public function calculate(float $pointALatitude,
                              float $pointALongitude,
                              float $pointBLatitude,
                              float $pointBLongitude): float
    {
        $theta              = abs($pointALongitude- $pointBLongitude);
        $distance           = sin(deg2rad($pointALatitude)) * sin(deg2rad($pointBLatitude)) +
                                (cos(deg2rad($pointALatitude)) * cos(deg2rad($pointBLatitude)) * cos(deg2rad($theta)));
        $distance           = acos($distance);
        $distance           = rad2deg($distance);
        $distance           = $distance * 60 * 1.1515 * 1.609344;
        $distance           = round($distance, 2);
        return $distance;
    }
}