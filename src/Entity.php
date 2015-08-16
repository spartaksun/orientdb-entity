<?php
/**
 * Created by A.Belyakovskiy.
 * Date: 8/16/15
 * Time: 3:20 PM
 */

namespace spartaksun\OrientDb;


use PhpOrient\Protocols\Binary\Data\ID;

/**
 * Entity
 * @package spartaksun\OrientDb
 */
abstract class Entity implements EntityInterface
{
    /**
     * @var ID
     */
    private $_rid;

    /**
     * @var array
     */
    private $_attributes = [];

    /**
     * @var array
     */
    private $_errors = [];


    /**
     * {@inheritdoc}
     */
    public function getAttribute($attributeName)
    {
        if (array_key_exists($attributeName, $this->_attributes)) {
            return $this->_attributes[$attributeName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($attributeName)
    {
        return $this->getAttribute($attributeName);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    final public function getRid()
    {
        return $this->_rid;
    }

    /**
     * {@inheritdoc}
     */
    final public function setRid($rid)
    {
        if ($rid instanceof ID) {
            $this->_rid = $rid;
        } else {
            if (!preg_match_all("~^\#(\d+):(\d+)$~", $rid, $matches)) {
                throw new Exception('Incorrect @rid string.');
            }

            $id = new ID();
            $id->cluster = $matches[1];
            $id->position = $matches[2];

            $this->_rid = $id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function validators()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * {@inheritdoc}
     */
    public function addError($attribute, $error)
    {
        $this->_errors[$attribute][] = $error;
    }

}
