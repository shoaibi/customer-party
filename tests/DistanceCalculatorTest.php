<?php
namespace Intercom\Tests;
use Intercom\DistanceCalculator;

class DistanceCalculatorTest extends BaseTest
{
    public function testCalculate()
    {
        $pointA             = [53.3381985, -6.2592576];
        $pointB             = [53.74452, -7.11167];
        $expectedDistance   = 72.20;
        $calculatedDistance = (new DistanceCalculator())->calculate($pointA[0], $pointA[1], $pointB[0], $pointB[1]);
        $this->assertEquals($expectedDistance, $calculatedDistance);
    }

    /**
     * @depends testCalculate
     */
    public function testCalculateGivesAbsoluteValue()
    {
        $pointA             = [53.74452, -7.11167];
        $pointB             = [53.3381985, -6.2592576];
        $expectedDistance   = 72.20;
        $calculatedDistance = (new DistanceCalculator())->calculate($pointA[0], $pointA[1], $pointB[0], $pointB[1]);
        $this->assertEquals($expectedDistance, $calculatedDistance);
    }
}