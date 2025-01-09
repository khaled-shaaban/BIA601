<?php

namespace App;

class BruteForce 
{
    public static $maxProfit;
    public static $maxProfitWeight;
    public static $bestSolution;
    public static function completeToN($binary, $bitCount)
    {
        $remainingBits = $bitCount - strlen($binary);
        if ($remainingBits > 0) {
            return str_repeat('0', $remainingBits) . $binary;
        } else {
            return $binary;
        }
    }
    public static function getProfit($completeSolution, $items) {
        //You must apply CompleteToN function on the solution before passing it
        if (strlen($completeSolution) != count($items)) {
            $completeSolution = self::completeToN($completeSolution, count($items));
        }
    
        $profit = 0;
        $v = -1;
        for ($i = 0; $i < count($items); $i++) {
            $v = ord($completeSolution[$i]) - 48;
            $profit += ($v * $items[$i]['profit']);
        }
    
        return $profit;
    }
    public static function getWeight($completeSolution, $items) {
        //You must apply CompleteToN function on the solution before passing it
        if (strlen($completeSolution) != count($items)) {
            $completeSolution = self::completeToN($completeSolution, count($items));
        }
    
        $weight = 0;
        $v = -1;
        for ($i = 0; $i < count($items); $i++) {
            $v = ord($completeSolution[$i]) - 48;
            $weight += ($v * $items[$i]['weight']);
        }
    
        return $weight;
    }
}