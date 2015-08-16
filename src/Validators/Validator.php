<?php
/**
 * Created by A.Belyakovskiy.
 * Date: 8/16/15
 * Time: 10:24 PM
 */

namespace spartaksun\OrientDb\Validators;


use spartaksun\OrientDb\EntityInterface;

/**
 * Abstract validator
 * @package spartaksun\OrientDb\Validators
 */
abstract class Validator
{

    /**
     * @param array $params
     */
    public function __construct($params = [])
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param $object
     * @param $attribute
     * @return mixed
     */
    abstract public function validateAttribute($object, $attribute);

    /**
     * @param EntityInterface $object
     * @param $attribute
     * @param $message
     * @param array $params
     */
    protected function addError(EntityInterface $object, $attribute, $message, $params = [])
    {
        $params['{attribute}'] = $attribute;
        $object->addError($attribute, strtr($message, $params));
    }

    /**
     * @param $value
     * @param bool $trim
     * @return bool
     */
    protected function isEmpty($value, $trim = false)
    {
        return $value === null || $value === array() || $value === '' || $trim && is_scalar($value) && trim($value) === '';
    }

}