<?php
/**
 * Created by A.Belyakovskiy.
 * Date: 8/16/15
 * Time: 10:17 PM
 */

namespace spartaksun\OrientDb\Validators;

/**
 * String validator
 * @package spartaksun\OrientDb\Validators
 */
class StringValidator extends Validator
{

    /**
     * @var integer maximum length
     */
    public $max;

    /**
     * @var integer minimum length
     */
    public $min;

    /**
     * @var integer exact length
     */
    public $is;

    /**
     * @var boolean whether the attribute value can be null or empty
     */
    public $allowEmpty = true;


    /**
     * @param $object
     * @param $attribute
     * @return mixed|void
     */
    public function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if ($this->allowEmpty && $this->isEmpty($value))
            return;

        if (is_array($value)) {
            $this->addError($object, $attribute, '{attribute} is invalid.');
            return;
        }

        if (function_exists('mb_strlen'))
            $length = mb_strlen($value, 'utf-8');
        else
            $length = strlen($value);

        if ($this->min !== null && $length < $this->min) {
            $message = '{attribute} is too short (minimum is {min} characters).';
            $this->addError($object, $attribute, $message, ['{min}' => $this->min]);
        }
        if ($this->max !== null && $length > $this->max) {
            $message = '{attribute} is too long (maximum is {max} characters).';
            $this->addError($object, $attribute, $message, ['{max}' => $this->max]);
        }
        if ($this->is !== null && $length !== $this->is) {
            $message = '{attribute} is of the wrong length (should be {length} characters).';
            $this->addError($object, $attribute, $message, ['{length}' => $this->is]);
        }
    }

}

