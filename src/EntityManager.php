<?php
/**
 * Created by A.Belyakovskiy.
 * Date: 8/16/15
 * Time: 2:21 PM
 */

namespace spartaksun\OrientDb;


use PhpOrient\PhpOrient;

/**
 * Class OrientDbEntityManager
 * @package spartaksun\OrientDb
 */
class EntityManager
{

    /**
     * @var PhpOrient
     */
    protected $client;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var array class mapping
     */
    public $classMap = [];


    /**
     * @param PhpOrient $client
     */
    public function __construct(PhpOrient $client, $dbName)
    {
        $this->client = $client;
        $this->dbName = $dbName;
    }

    /**
     * @param string $orientClassName
     * @return Repository
     */
    public function getRepository($orientClassName)
    {
        $classMap = array_flip($this->classMap);

        return new Repository($classMap[$orientClassName], $this);
    }

    /**
     * @return PhpOrient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

}