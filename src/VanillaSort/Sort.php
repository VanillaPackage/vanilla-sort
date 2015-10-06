<?php

namespace Rentalhost\VanillaSort;

use Closure;
use ReflectionFunction;

/**
 * Class Sort
 * @package Rentalhost\VanillaSort
 */
class Sort
{
    /**
     * Stores sorting object.
     * @var array
     */
    private $object;

    /**
     * Stores sorting controllers.
     * @var array
     */
    private $sorters = [ ];

    /**
     * Sort constructor.
     */
    private function __construct()
    {
    }

    /**
     * Define a new sorter to object.
     *
     * @param callback|Closure|string $function The sorter controller.
     * @param int                     $order    Can be SORT_ASC or SORT_DESC.
     *
     * @return $this
     */
    public function by($function, $order = SORT_ASC)
    {
        if ($function instanceof Closure ||
            is_array($function)
        ) {
            /** @noinspection NotOptimalIfConditionsInspection */
            if (is_array($function)) {
                $function = $function[0];
            }

            $functionReflection = new ReflectionFunction($function);
            if ($functionReflection->getNumberOfParameters() === 1) {
                $function = self::closureUnaryFunction($function);
            }
        }
        else {
            $function = self::closurePropertyName($function);
        }

        $this->sorters[] = [
            'function' => $function,
            'order' => $order,
        ];

        return $this;
    }

    /**
     * Do the sorting and return the object sortered.
     * @return array
     */
    public function get()
    {
        $sorters = $this->sorters;
        $object = (array) $this->object;

        uasort($object, function ($v1, $v2) use ($sorters, $object) {
            // Try sort each objects by the sorter.
            foreach ($sorters as $sorter) {
                $result = call_user_func($sorter['function'], $v1, $v2);
                if ($result !== 0) {
                    return $sorter['order'] === SORT_DESC ? -$result : $result;
                }
            }

            // In last case, order by original key order.
            return array_search($v1, $object, true) < array_search($v2, $object, true) ? -1 : 1;
        });

        return $object;
    }

    /**
     * Return a default comparator from a unary comparator.
     *
     * @param Closure $function Function to convert to default comparator.
     *
     * @return Closure
     */
    private static function closureUnaryFunction($function)
    {
        return function ($v1, $v2) use ($function) {
            $v1 = $function($v1);
            $v2 = $function($v2);

            if ($v1 == $v2) {
                return 0;
            }

            return $v1 < $v2 ? -1 : 1;
        };
    }

    /**
     * Return a default comparator to compare property names.
     *
     * @param string $propertyName Property name to test.
     *
     * @return Closure
     */
    private static function closurePropertyName($propertyName)
    {
        return function ($v1, $v2) use ($propertyName) {
            $v1 = isset( $v1 [$propertyName] ) ? $v1 [$propertyName] : null;
            $v2 = isset( $v2 [$propertyName] ) ? $v2 [$propertyName] : null;

            if ($v1 == $v2) {
                return 0;
            }

            return $v1 < $v2 ? -1 : 1;
        };
    }

    /**
     * Create a Sort instance by using an array.
     *
     * @param array $array Array to prepare sorting.
     *
     * @return $this
     */
    public static function using($array)
    {
        $instance = new self;
        $instance->object = $array;

        return $instance;
    }
}
