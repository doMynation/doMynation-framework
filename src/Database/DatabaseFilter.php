<?php

namespace Domynation\Database;

/**
 * Class DatabaseFilter
 *
 * @package Domynation\Database
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class DatabaseFilter
{

    /**
     * @var array
     */
    protected $value;

    /**
     * DatabaseFilter constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = is_array($value) ? $value : [$value];
    }

    /**
     * Creates a new instance of the filter from a form's
     * inputs. This is where any transformation should occur.
     *
     * @param $data
     *
     * @return DatabaseFilter
     */
    public static function fromForm($data)
    {
        return new static($data);
    }

    /**
     * Validates the inputs passed to the filter.
     *
     * @param array $data
     *
     * @return bool
     */
    public static function validate($data)
    {
        return true;
    }
}