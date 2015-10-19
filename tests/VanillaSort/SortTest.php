<?php

namespace Rentalhost\VanillaSort;

use PHPUnit_Framework_TestCase;
use Rentalhost\VanillaSort\Stubs\AggregatedArray;

/**
 * Class SortTest
 * @package Rentalhost\VanillaSort
 */
class SortTest extends PHPUnit_Framework_TestCase
{
    /**
     * Returns city data.
     * @return array
     */
    private static function getCityData()
    {
        return [
            [ 'id' => 7, 'name' => 'Amsterdam', 'population' => 750000, 'country' => 'Netherlands' ],
            [ 'id' => 12, 'name' => 'The Hague', 'population' => 450000, 'country' => 'Netherlands' ],
            [ 'id' => 43, 'name' => 'Rotterdam', 'population' => 600000, 'country' => 'Netherlands' ],
            [ 'id' => 5, 'name' => 'Berlin', 'population' => 3000000, 'country' => 'Germany' ],
            [ 'id' => 42, 'name' => 'Düsseldorf', 'population' => 550000, 'country' => 'Germany' ],
            [ 'id' => 44, 'name' => 'Stuttgard', 'population' => 600000, 'country' => 'Germany' ],
        ];
    }

    /**
     * Return an array containing only the specified column.
     *
     * @param array  $array  Array to pluck.
     * @param string $column Column name.
     *
     * @return array
     */
    private static function arrayPluck($array, $column)
    {
        $results = [ ];

        foreach ($array as $item) {
            $results[] = array_key_exists($column, $item) ? $item[$column] : null;
        }

        return $results;
    }

    /**
     * Basic sorting: sort numbers.
     * @covers \Rentalhost\VanillaSort\Sort::__construct
     * @covers \Rentalhost\VanillaSort\Sort::using
     * @covers \Rentalhost\VanillaSort\Sort::by
     * @covers \Rentalhost\VanillaSort\Sort::get
     */
    public function testBasicSortingNumber()
    {
        $sort = Sort::using([ 3, 2, 1, 6, 5, 4 ])->by(function ($v1, $v2) {
            return $v1 - $v2;
        })->get();

        static::assertSame([ 1, 2, 3, 4, 5, 6 ], array_values($sort));
    }

    /**
     * Basic sorting: sort numbers DESC.
     * @coversNothing
     */
    public function testBasicSortingNumberDesc()
    {
        $sort = Sort::using([ 3, 2, 1, 6, 5, 4 ])->by(function ($v1, $v2) {
            return $v1 - $v2;
        }, SORT_DESC)->get();

        static::assertSame([ 6, 5, 4, 3, 2, 1 ], array_values($sort));
    }

    /**
     * Basic sorting: sort names.
     * @coversNothing
     */
    public function testBasicSortingNames()
    {
        $sort = Sort::using([ 'c', 'b', 'a', 'f', 'e', 'd' ])->by(function ($v1, $v2) {
            return strcmp($v1, $v2);
        })->get();

        static::assertSame([ 'a', 'b', 'c', 'd', 'e', 'f' ], array_values($sort));
    }

    /**
     * Basic sorting: sort names DESC.
     * @coversNothing
     */
    public function testBasicSortingNamesDesc()
    {
        $sort = Sort::using([ 'c', 'b', 'a', 'f', 'e', 'd' ])->by(function ($v1, $v2) {
            return strcmp($v1, $v2);
        }, SORT_DESC)->get();

        static::assertSame([ 'f', 'e', 'd', 'c', 'b', 'a' ], array_values($sort));
    }

    /**
     * Default sorting: sort by ID
     * @coversNothing
     */
    public function testSortById()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return $v1['id'] - $v2['id'];
        })->get();

        static::assertSame([
            'Berlin',
            'Amsterdam',
            'The Hague',
            'Düsseldorf',
            'Rotterdam',
            'Stuttgard',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by ID DESC.
     * @coversNothing
     */
    public function testSortByIdDesc()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return $v1['id'] - $v2['id'];
        }, SORT_DESC)->get();

        static::assertSame([
            'Stuttgard',
            'Rotterdam',
            'Düsseldorf',
            'The Hague',
            'Amsterdam',
            'Berlin',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by ID, using unary functions.
     * @covers \Rentalhost\VanillaSort\Sort::closureUnaryFunction
     */
    public function testSortByIdUsingUnaryFunctions()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return $v['id'];
        })->get();

        static::assertSame([
            'Berlin',
            'Amsterdam',
            'The Hague',
            'Düsseldorf',
            'Rotterdam',
            'Stuttgard',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by ID DESC, using unary functions.
     * @coversNothing
     */
    public function testSortByIdDescUsingUnaryFunctions()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return $v['id'];
        }, SORT_DESC)->get();

        static::assertSame([
            'Stuttgard',
            'Rotterdam',
            'Düsseldorf',
            'The Hague',
            'Amsterdam',
            'Berlin',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by Country, then by Population.
     * @coversNothing
     */
    public function testSortByCountryThenByPopulation()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return strcmp($v1['country'], $v2['country']);
        })->by(function ($v1, $v2) {
            return $v1['population'] - $v2['population'];
        })->get();

        static::assertSame([
            'Düsseldorf',
            'Stuttgard',
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Amsterdam',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by Country, then by Population, using unary functions.
     * @covers \Rentalhost\VanillaSort\Sort::closureUnaryFunction
     */
    public function testSortByCountryThenByPopulationUsingUnaryFunctions()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return $v['country'];
        })->by(function ($v) {
            return $v['population'];
        })->get();

        static::assertSame([
            'Düsseldorf',
            'Stuttgard',
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Amsterdam',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by length of name, then by population, then by ID.
     * @covers \Rentalhost\VanillaSort\Sort::get
     */
    public function testSortByLengthOfNameThenByPopulationThenById()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return strlen($v1['name']) - strlen($v2['name']);
        })->by(function ($v1, $v2) {
            return $v1['population'] - $v2['population'];
        })->by(function ($v1, $v2) {
            return $v1['id'] - $v2['id'];
        })->get();

        static::assertSame([
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Stuttgard',
            'Amsterdam',
            'Düsseldorf',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Default sorting: sort by length of name, then by population, then by ID, using unary functions.
     * @coversNothing
     */
    public function testSortByLengthOfNameThenByPopulationThenByIdUsingUnaryFunctions()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return strlen($v['name']);
        })->by(function ($v) {
            return $v['population'];
        })->by(function ($v) {
            return $v['id'];
        })->get();

        static::assertSame([
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Stuttgard',
            'Amsterdam',
            'Düsseldorf',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property name sorting: sort by Country, then by Population.
     * @covers \Rentalhost\VanillaSort\Sort::closurePropertyName
     */
    public function testPropertyNameSortByCountryThenByPopulation()
    {
        $sort = Sort::using(static::getCityData())->by('country')->by('population')->get();

        static::assertSame([
            'Düsseldorf',
            'Stuttgard',
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Amsterdam',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property name sorting: sort by Country DESC, then by Population.
     * @coversNothing
     */
    public function testPropertyNameSortByCountryDescThenByPopulation()
    {
        $sort = Sort::using(static::getCityData())->by('country', SORT_DESC)->by('population')->get();

        static::assertSame([
            'The Hague',
            'Rotterdam',
            'Amsterdam',
            'Düsseldorf',
            'Stuttgard',
            'Berlin',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property name sorting: sort by Country, then by Population DESC.
     * @coversNothing
     */
    public function testPropertyNameSortByCountryThenByPopulationDesc()
    {
        $sort = Sort::using(static::getCityData())->by('country')->by('population', SORT_DESC)->get();

        static::assertSame([
            'Berlin',
            'Stuttgard',
            'Düsseldorf',
            'Amsterdam',
            'Rotterdam',
            'The Hague',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property and Callback sorting: sort by name length, then by Population.
     * @covers \Rentalhost\VanillaSort\Sort::get
     */
    public function testPropertyAndCallbackSortByNameLengthThenByPopulation()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return strlen($v1['name']) - strlen($v2['name']);
        })->by('population')->get();

        static::assertSame([
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Stuttgard',
            'Amsterdam',
            'Düsseldorf',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property and Callback sorting: sort by name length using a unary function, then by Population.
     * @coversNothing
     */
    public function testPropertyAndCallbackSortByNameLengthThenByPopulationUsingUnaryFunction()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return strlen($v['name']);
        })->by('population')->get();

        static::assertSame([
            'Berlin',
            'The Hague',
            'Rotterdam',
            'Stuttgard',
            'Amsterdam',
            'Düsseldorf',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property and Callback sorting: sort by name length DESC, then by Population.
     * @coversNothing
     */
    public function testPropertyAndCallbackSortByNameDescThenByPopulation()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return strlen($v1['name']) - strlen($v2['name']);
        }, SORT_DESC)->by('population')->get();

        static::assertSame([
            'Düsseldorf',
            'The Hague',
            'Rotterdam',
            'Stuttgard',
            'Amsterdam',
            'Berlin',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property and Callback sorting: sort by name length DESC using a unary function, then by Population.
     * @coversNothing
     */
    public function testPropertyAndCallbackSortByNameLengthDescUsingUnaryFunctionThenByPopulation()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return strlen($v['name']);
        }, SORT_DESC)->by('population')->get();

        static::assertSame([
            'Düsseldorf',
            'The Hague',
            'Rotterdam',
            'Stuttgard',
            'Amsterdam',
            'Berlin',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property and Callback sorting: sort by name length, then by Population DESC.
     * @coversNothing
     */
    public function testPropertyAndCallbackSortByNameLengthThenByPopulationDesc()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v1, $v2) {
            return strlen($v1['name']) - strlen($v2['name']);
        })->by('population', SORT_DESC)->get();

        static::assertSame([
            'Berlin',
            'Amsterdam',
            'Rotterdam',
            'Stuttgard',
            'The Hague',
            'Düsseldorf',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Property and Callback sorting: sort by name length, then by Population DESC.
     * @coversNothing
     */
    public function testPropertyAndCallbackSortByNameLengthUsingUnaryFunctionThenByPopulationDesc()
    {
        $sort = Sort::using(static::getCityData())->by(function ($v) {
            return strlen($v['name']);
        })->by('population', SORT_DESC)->get();

        static::assertSame([
            'Berlin',
            'Amsterdam',
            'Rotterdam',
            'Stuttgard',
            'The Hague',
            'Düsseldorf',
        ], self::arrayPluck($sort, 'name'));
    }

    /**
     * Special sorting: sort by undefined key.
     * @covers \Rentalhost\VanillaSort\Sort::by
     */
    public function testSpecialSortingByUndefinedKey()
    {
        $sort = Sort::using([ 'aaa', 'bb', 'c' ])->by('strlen')->get();

        static::assertSame([ 'aaa', 'bb', 'c' ], array_values($sort));
    }

    /**
     * Special sorting: sort by undefined key DESC.
     * @coversNothing
     */
    public function testSpecialSortingByUndefinedKeyDesc()
    {
        $sort = Sort::using([ 'aaa', 'bb', 'c' ])->by('strlen', SORT_DESC)->get();

        static::assertSame([ 'aaa', 'bb', 'c' ], array_values($sort));
    }

    /**
     * Special sorting: sort by a callable.
     * @covers \Rentalhost\VanillaSort\Sort::by
     */
    public function testSpecialSortingByCallable()
    {
        $sort = Sort::using([ 'aaa', 'bb', 'c' ])->by([ 'strlen' ])->get();

        static::assertSame([ 'c', 'bb', 'aaa' ], array_values($sort));
    }

    /**
     * Special sorting: sort by a callable DESC.
     * @coversNothing
     */
    public function testSpecialSortingByCallableDesc()
    {
        $sort = Sort::using([ 'aaa', 'bb', 'c' ])->by([ 'strlen' ], SORT_DESC)->get();

        static::assertSame([ 'aaa', 'bb', 'c' ], array_values($sort));
    }

    /**
     * Special sorting: sort by mixed data.
     * @coversNothing
     */
    public function testSpecialSortingByMixedData()
    {
        $sort = Sort::using([ false, 3, '2', true ])->by(function ($v1) {
            return (int) $v1;
        })->get();

        static::assertSame([ false, true, '2', 3 ], array_values($sort));
    }

    /**
     * Special sorting: sort by mixed data.
     * @coversNothing
     */
    public function testSpecialSortingByMixedDataSimilar()
    {
        $sort = Sort::using([ '2', 3, 2 ])->by(function ($v1) {
            return $v1;
        })->get();

        static::assertSame([ '2', 2, 3 ], array_values($sort));
    }

    /**
     * Special sorting: sort by mixed data.
     * @coversNothing
     */
    public function testSpecialSortingByMixedPropertySimilar()
    {
        $sort = Sort::using([ [ 'data' => '2' ], [ 'data' => 3 ], [ 'data' => 2 ] ])->by('data')->get();

        static::assertSame([ [ 'data' => '2' ], [ 'data' => 2 ], [ 'data' => 3 ] ], array_values($sort));
    }

    /**
     * Special sorting: sort by mixed data DESC.
     * @coversNothing
     */
    public function testSpecialSortingByMixedPropertySimilarDesc()
    {
        $sort = Sort::using([ [ 'data' => '2' ], [ 'data' => 3 ], [ 'data' => 2 ] ])->by('data', SORT_DESC)->get();

        static::assertSame([ [ 'data' => 3 ], [ 'data' => '2' ], [ 'data' => 2 ] ], array_values($sort));
    }

    /**
     * Special sorting: sort by none.
     * @coversNothing
     */
    public function testSpecialSortingByNone()
    {
        $sort = Sort::using([ false, 3, '2', true ])->get();

        static::assertSame([ false, 3, '2', true ], array_values($sort));
    }

    /**
     * Special sorting: sort by none, and reverse result.
     * @covers \Rentalhost\VanillaSort\Sort::get
     */
    public function testSpecialSortingByNoneAndSortDescResult()
    {
        $sort = Sort::using([ false, 3, '2', true ])->get(SORT_DESC);

        static::assertSame([ true, '2', 3, false ], array_values($sort));
    }

    /**
     * Special sorting: sort using a IteratorAggregated instance.
     * @covers \Rentalhost\VanillaSort\Sort::using
     */
    public function testIteratorAggregated()
    {
        $sort = Sort::using(new AggregatedArray())->by(function ($item) {
            return $item;
        })->get();

        static::assertSame([ 1, 3, 5 ], array_values($sort));
    }
}
