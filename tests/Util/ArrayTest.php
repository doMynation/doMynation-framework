<?php

class ArrayTest extends PHPUnit_Framework_TestCase
{

    function arrayFlattenProvider()
    {
        return [
            [[[1, 2, [3, 4, [5, 6, 7], 8], [9], 10]], [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]],
            [[['How'], ['are', 'you']], ['How', 'are', 'you']],
            [['How', new Stdclass], ['How', new Stdclass]]
        ];
    }

    /**
     * @test
     */
    public function it_tests_array_partition()
    {
        $this->assertEquals([[1, 3, 5], [2, 4, 6]], array_partition([1, 2, 3, 4, 5, 6], function ($a) {
            return $a % 2 === 1;
        }));

        list($odd, $even) = array_partition([1, 2, 3, 4, 5, 6], function ($a) {
            return $a % 2 === 1;
        });

        $this->assertEquals([$odd, $even], [[1, 3, 5], [2, 4, 6]]);

        $this->assertEquals([[], []], array_partition([], function ($e) {
            return $e % 2 === 0;
        }));
    }

    /**
     * @test
     * @dataProvider arrayFlattenProvider
     */
    public function it_tests_array_flatten($input, $expected)
    {
        $this->assertEquals($expected, array_flatten($input));
    }


    /**
     * @test
     */
    public function it_tests_array_every()
    {
        $odds    = [1, 3, 5, 7, 9];
        $strings = ['hello', 'j', 'and', 1, 'string', true];

        $this->assertTrue(array_every($odds, function ($n) {
            return $n % 2 == 1;
        }));

        $this->assertFalse(array_every($strings, function ($n) {
            return is_string($n);
        }));
    }

    /**
     * @test
     */
    public function it_tests_array_some()
    {
        $numbers = [2, 4, 6, 7, 8, 10];
        $evens = [2, 4, 6, 8, 10];

        $this->assertTrue(array_some($numbers, function ($n) {
            return $n % 2 == 1;
        }));

        $this->assertFalse(array_some($evens, function ($n) {
            return $n % 2 == 1;
        }));
    }
}
