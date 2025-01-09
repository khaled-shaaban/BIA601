<?php

namespace App\Http\Controllers;

use App\Genetic;
use App\BruteForce;


class KnapsackController extends Controller
{
  public function solveKnapsack()
  {
    $data = request()->all();
  
    $w = $data['allowedWeight']; // knapsack capacity

    if (count($data['items']) > 6) {
      $response = $this->solveUsingGenetic($data);
    } else {
      $response = $this->solveUsingDynamic($data);
    }
    
    return response()->json($response);
  }

  private function solveUsingDynamic(array $data)
  {
    //w: knapsack space
    $bitCount = count($data['items']);
    $max_profit = -1; $curr_profit = -1;
    $max_profitWeight = -1; $curr_weight = -1;
    $best_solution = '';
    $charArray = array_fill(0, $bitCount, '1');
    $maximum = bindec(implode('', $charArray));
    
    //This loop to try every solution and find the best profits that fit w: space of knapsack
    for ($i = 0; $i <= $maximum; $i++){
        $binValue = decbin($i);
        $solution = BruteForce::completeToN($binValue, $bitCount);
        $curr_profit = BruteForce::getProfit($solution, $data['items']);
        $curr_weight = BruteForce::getWeight($solution, $data['items']);

        if ($curr_weight <= $data['allowedWeight']){
            if ($curr_profit > $max_profit){
                $max_profit = $curr_profit;
                $max_profitWeight = $curr_weight;
                $best_solution = $solution;
            }
        }
    }
    
    BruteForce::$bestSolution = $best_solution;
    BruteForce::$maxProfit = $max_profit;
    BruteForce::$maxProfitWeight = $max_profitWeight;

    $profit = BruteForce::$maxProfit;
    $weight = BruteForce::$maxProfitWeight;

    return [
      'profit' => $profit,
      'weight' => $weight,
      'solution' => $best_solution
    ];
  }

  private function solveUsingGenetic(array $data)
  {
    Genetic::setItems($data['items']);
    Genetic::setW($data['allowedWeight']);
    Genetic::initialPopulation();

    $parents = [];
    $offspring = [];
    $bestSolution = null;
    for ($i = 0; $i < 10; $i++) 
    {
      $parents = Genetic::selectParents();
      $offspring = Genetic::reProduction($parents);
      Genetic::mergeSort($offspring);
      Genetic::selectNewPopulation();

      $bestSolution = Genetic::$bestSolution;
    }
  
    $profit = Genetic::$maxProfit;
    $weight = Genetic::$maxProfitWeight;

    return [
      'profit' => $profit,
      'weight' => $weight,
      'solution' => $bestSolution
    ];
  }
}