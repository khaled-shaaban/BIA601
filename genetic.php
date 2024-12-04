<?php

class Genetic
{
    public const POPULATION_SIZE = 18; // Must even
    public const OFFSPRING_SIZE = 12; // Must even

    public static $maxProfit;
    public static $maxProfitWeight;
    public static $bestSolution;

    private static $items; //Array of items
    private static $w; // Space of knapsack
    public static $population = [];
    public static $popAndOffspring = []; // population and offsprings SORTED

    public static function setItems($value)
    {
        self::$items = $value;
    }
    public static function getItems()
    {
        return self::$items;
    }
    public static function getItemsCount()
    {
        return count(self::$items);
    }
    public static function setW($value)
    {
        self::$w = $value;
    }
    public static function getW()
    {
        return self::$w;
    }
    public static function longRandom($min, $max)
    {
        return random_int($min, $max);
    }
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
            $profit += ($v * $items[$i][2]);
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
            $weight += ($v * $items[$i][1]);
        }
    
        return $weight;
    }
    public static function fitness($chromosome) {
        $weight = self::getWeight($chromosome, self::getItems());
        if ($weight > self::getW()) { //Weight of items is bigger than knapsack space, so it is penalized
            return 0;
        } else {
            $profit = self::getProfit($chromosome, self::getItems());
            return $profit;
        }
    }
    public static function isExist($array, $value) {
        $find = false;
        foreach ($array as $item) {
            if ($item == $value) {
                $find = true;
                break;
            }
        }
        return $find;
    }
    public static function initialPopulation() {
        //This function takes items and generates a randomly group of population --> assign to population[]
        $bitsCount = self::getItemsCount();
        $charArray = array_fill(0, $bitsCount, '1');
        $maximum = bindec(implode('', $charArray));
        $populationInt = array_fill(0, self::POPULATION_SIZE, 0); //To save int for each chromosome
        $strongerChromosome = -1; //To assign the best solution to best_solution from initial population
    
        for ($i = 0; $i < self::POPULATION_SIZE; $i++) { 
            while (true) {
                $k = self::longRandom(0, $maximum);
                if (!self::isExist($populationInt, $k)) { //To stop repetition
                    $chromosome = decbin($k);
                    $chromosome = self::completeToN($chromosome, $bitsCount);
                    self::$population[$i] = $chromosome;
                    //echo $chromosome . '<br>';
                    $populationInt[$i] = $k;
    
                    //To assign the best solution to best_solution from initial population
                    if (self::fitness($chromosome) > $strongerChromosome) {
                        $strongerChromosome = self::fitness($chromosome);
                        self::$bestSolution = $chromosome;
                    }
    
                    break;
                }
            } 
        }
        self::$maxProfit = self::fitness(self::$bestSolution);
        self::$maxProfitWeight = self::getWeight(self::$bestSolution, self::getItems());
    }
    public static function sortAsFitness(&$arr) {
        for ($i = 0; $i < count($arr); $i++) {
            for ($j = 0; $j < count($arr) - 1; $j++) {
                if (self::fitness($arr[$j]) < self::fitness($arr[$j + 1])) {
                    $temp = $arr[$j];
                    $arr[$j] = $arr[$j + 1];
                    $arr[$j + 1] = $temp;
                }
            }
        }
    }
    public static function selectParents() {
        //This function return parents[] with lenght == OFFSPRING_SIZE
        //After sort population per Fitness --> it select the chromosome with highest fitness
        $parents = array_fill(0, self::OFFSPRING_SIZE, '');
        self::sortAsFitness(self::$population);
        for ($i = 0; $i < self::OFFSPRING_SIZE; $i++) {
            $parents[$i] = self::$population[$i];
        }

        return $parents;
    }
    public static function SpCrossover($parent1, $parent2) {
        $len = strlen($parent1);
        $min = 1;
        $max = $len - 2;
        $point = rand($min, $max);
        $sb1 = '';
        $sb2 = '';
    
        for ($i = 0; $i < $len; $i++) {
            if ($i <= $point) {
                $sb1 .= $parent1[$i];
                $sb2 .= $parent2[$i];
            } else {
                $sb1 .= $parent2[$i];
                $sb2 .= $parent1[$i];
            }
        }
        return [$sb1, $sb2];
    }
    public static function mutation($chromosome) {
        // Rate of chromosome = 0.1666666667
        $mutationRate = 0.1666666667;
        $geneCount = (int)(strlen($chromosome) * $mutationRate);
        $count = 1;
        $chromosomeArray = str_split($chromosome);
    
        while ($count <= $geneCount) {
            $index = rand(0, self::getItemsCount() - 1);
            if ($chromosomeArray[$index] == '0') {
                $chromosomeArray[$index] = '1';
            } else {
                $chromosomeArray[$index] = '0';
            }
            $count++;
        }
        $result = implode('', $chromosomeArray);
        return $result;
    }
    public static function reProduction($parents) {
        $offSpring = array_fill(0, self::OFFSPRING_SIZE, '');
        //CROSSOVER:
        for ($i = 0; $i < count($offSpring); $i += 2) {
            $children = self::SpCrossover($parents[$i], $parents[$i + 1]);
            $offSpring[$i] = $children[0];
            $offSpring[$i + 1] = $children[1];
        }
        //MUTATION:
        for ($i = 0; $i < count($offSpring); $i++) {
            $offSpring[$i] = self::mutation($offSpring[$i]);
        }

        return $offSpring;
    }
    public static function mergeSort($offspring) {
        $size = self::POPULATION_SIZE + self::OFFSPRING_SIZE;
        self::$popAndOffspring = array_fill(0, $size, '');
        for ($i = 0; $i < $size; $i++) {
            if ($i < self::POPULATION_SIZE) {
                self::$popAndOffspring[$i] = self::$population[$i];
            }
            else {
                self::$popAndOffspring[$i] = $offspring[$i - self::POPULATION_SIZE];
            }
        }

        self::sortAsFitness(self::$popAndOffspring);
    }
    public static function selectNewPopulation() {
        //Then choose first POPULATION_SIZE from popAndOffspring and return it to population ((new generation))
        //Return the POPULATION_SIZE from array --> return to population AS a new generation
        //Return bestsolution from this array

        for ($i = 0; $i < self::POPULATION_SIZE; $i++) {
            self::$population[$i] = self::$popAndOffspring[$i];
        }
        self::$bestSolution = self::$population[0];
        self::$maxProfit = self::fitness(self::$bestSolution);
        self::$maxProfitWeight = self::getWeight(self::$bestSolution, self::getItems());
    }
}

function solveKnapsack($items, $w, $loop) {
    Genetic::setItems($items);
    Genetic::setW($w);
    Genetic::initialPopulation();
    $parents = [];
    $offspring = [];
    for ($i = 0; $i < $loop; $i++) {
        $parents = Genetic::selectParents();
        $offspring = Genetic::reProduction($parents);
        Genetic::mergeSort($offspring);
        Genetic::selectNewPopulation();
        
        return Genetic::$bestSolution;
    }
}

// 0: name, 1: weight, 2: profit
$items = [
    ['1',5,10], ['2',8,12], ['3',4,8], ['4',3,5], ['5',9,15], ['6',7,10]
];
$w = 20;

$bestSolution = solveKnapsack($items, $w, 10);
$profit = Genetic::$maxProfit;
$weight = Genetic::$maxProfitWeight;
echo 'Best solution: ' . $bestSolution . '<br>';
echo 'Profit: ' . $profit . '<br>';
echo 'Weight: ' . $weight . '<br>';