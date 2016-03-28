<?php

class FunctionalTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_tests_curry()
    {
        $add = function ($a, $b) {
            return $a + $b;
        };

        $threeParams = function ($a, $b, $c) {
            return $a + $b + $c;
        };

        $addTwo  = curry($add, 2);
        $addFive = curry($threeParams, 5);

        $this->assertEquals(5, $addTwo(3));
        $this->assertEquals(10, $addFive(3, 2));
    }

    /**
     * @test
     */
    public function it_tests_compose()
    {
        $string   = "Hello I love things and stuff";
        $expected = "HELLO-I-LOVE-THINGS-AND-STUFF";

        $toUpper = function ($i) {
            return strtoupper($i);
        };

        $splitBySpaces = function ($i) {
            return preg_split("/\s/", $i);
        };

        $mergeWithDash = function ($a) {
            return implode('-', $a);
        };

        $capitalDashed = compose($mergeWithDash, $splitBySpaces, $toUpper);

        $this->assertEquals($expected, $capitalDashed($string));
    }
}
