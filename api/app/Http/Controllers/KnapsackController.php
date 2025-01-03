<?php

namespace App\Http\Controllers;

use App\Genetic;


class KnapsackController extends Controller
{
  public function solveKnapsack()
  {
    $data = request()->all();
  
    $w = $data['allowedWeight']; // knapsack capacity
    
    Genetic::setItems($data['items']);
    Genetic::setW($w);
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
    
    return response()->json([
      'profit' => $profit,
      'weight' => $weight,
      'solution' => $bestSolution
    ]);
  }
}