<?php

class EloCalculator {
    const K = 32;
    
    private static $adder;
    
    public static function calculate($elo1, $score1, $elo2, $score2) {
	self::$adder = 0.5 / MAX_HALF_ROUNDS;

        $scoreResults = self::getScores($score1, $score2);
        $score1 = $scoreResults['score1'];
        $score2 = $scoreResults['score2'];
        
        $transformedElo1 = self::transformElo($elo1);
        $transformedElo2 = self::transformElo($elo2);
        
        $expectedScore1 = self::getExpectedScore($transformedElo1, $transformedElo2);
        $expectedScore2 = self::getExpectedScore($transformedElo2, $transformedElo1);
        
        return [
            'elo1' => self::getNewElo($elo1, $score1, $expectedScore1),
            'elo2' => self::getNewElo($elo2, $score2, $expectedScore2),
        ];
    }
    
    private static function transformElo($elo) {
        return pow(10, ($elo/400));
    }
    
    private static function getExpectedScore($transformedElo1, $transformedElo2) {
        return $transformedElo1 / ($transformedElo1 + $transformedElo2);
    }
    
    private static function getNewElo($elo, $score, $expectedScore) {
        return round($elo + self::K * ($score - $expectedScore));
    }
    
    private static function getScores($score1, $score2) {
        if ($score1 > $score2) {
            $highScore = $score1;
            $lowScore = $score2;
        } else {
            $highScore = $score2;
            $lowScore = $score1;
        }
                
        if ($score1 > MAX_ROUNDS || $score2 > MAX_ROUNDS) {
            $scoreAdder = 0;
        } else {
            $deltaScore = abs($score1 - $score2);
            
            if ($deltaScore <= MAX_HALF_ROUNDS) {
                $scoreAdder = 0;
            } else {
                $scoreAdder = ($deltaScore - MAX_HALF_ROUNDS) * self::$adder;
            }
        }
        
        if ($score1 == $highScore) {
            $return = ['score1' => 1 + $scoreAdder, 'score2' => 0 - $scoreAdder];
        } else {
            $return = ['score1' => 0 - $scoreAdder, 'score2' => 1 + $scoreAdder];
        }
        
        return $return;
    }
}
