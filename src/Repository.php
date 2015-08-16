<?php
/**
 * Created by A.Belyakovskiy.
 * Date: 8/16/15
 * Time: 2:21 PM
 */

namespace spartaksun\OrientDb;


use Doctrine\OrientDB\Query\Query;
use OrientDbBundle\Validators\Validator;
use PhpOrient\Protocols\Binary\Data\Record;

/**
 * Class OrientDbRepository
 * @package spartaksun\OrientDb
 */
class Repository
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $dbClass;

    /**
     * @param $dbClass string
     * @param EntityManager $em
     */
    function __construct($dbClass, EntityManager $em)
    {
        $this->em = $em;
        $this->dbClass = $dbClass;
    }

    /**
     * @param int $limit
     * @return null
     */
    public function findAll($limit = 20)
    {
        $sql = (new Query())->from([$this->dbClass])
            ->limit($limit)
            ->getRaw();

        $data = $this->prepareClient()
            ->query($sql);

        return $this->populate($data);
    }

    /**
     * @param $condition
     * @param $params
     * @return EntityInterface
     */
    public function find($condition, $params)
    {
        $sql = (new Query())->from([$this->dbClass])
            ->where($condition, $params)
            ->limit(1)
            ->getRaw();

        $data = $this->prepareClient()->query($sql);
        $collection = $this->populate($data);

        return array_pop($collection);
    }

    /**
     * @param $object
     * @return bool|\stdClass
     */
    public function persist(EntityInterface $object)
    {
        $this->validate($object);

        if (!empty($object->getErrors())) {
            return false;
        }

        if (empty($object->getRid())) {
            $object->setRid(
                $this->insert($object->getAttributes())
            );
        } else {
            //TODO Update entity
        }

        return true;
    }

    /**
     * Runs all object validators
     * @param EntityInterface $object
     * @throws \ErrorException
     */
    protected function validate(EntityInterface $object)
    {
        $validators = $object->validators();
        foreach ($validators as $attribute => $attributeValidators) {
            foreach($attributeValidators as $validatorParams) {
                $validator = $this->buildValidators($validatorParams);
                $validator->validateAttribute($object, $attribute);
            }
        }
    }

    /**
     * Compose validator from parameters
     * @param array $validatorParams
     * @return \spartaksun\OrientDb\Validators\Validator
     * @throws \ErrorException
     */
    protected function buildValidators(array $validatorParams)
    {
        $params = [];
        if (isset($validatorParams[0]) && class_exists($validatorParams[0])) {
            if (!empty($validatorParams[1]) && is_array($validatorParams[1])) {
                $params = $validatorParams[1];
            }

            return new $validatorParams[0]($params);
        } else {
            throw new Exception('Incorrect validator class');
        }
    }

    /**
     * @param $params
     * @return EntityInterface
     * @throws \Exception
     */
    public function insert($params)
    {
        $sql = (new Query())->insert()
            ->into($this->dbClass)
            ->fields(array_keys($params))
            ->values(array_values($params))
            ->getRaw();

        $record = $this->prepareClient()->command($sql);
        /* @var $record Record */

        return $record->getRid();
    }

    /**
     * @return \PhpOrient\PhpOrient
     */
    protected function prepareClient()
    {
        $client = $this->em->getClient();
        $client->connect();
        $client->dbOpen($this->em->getDbName());

        return $client;
    }

    /**
     * @param $objects
     * @return null
     */
    protected function populate(array $objects)
    {
        $entities = [];
        foreach ($objects as $object) {
            $entities[] = $this->populateRecord($object);
        }

        return $entities;
    }

    /**
     * @param $record
     * @return \stdClass
     * @throws \Exception
     */
    protected function populateRecord(Record $record)
    {
        $classMap = $this->em->classMap;
        $key = $record->getOClass();
        if (array_key_exists($key, $classMap)) {

            $entity = new $classMap[$key];
            /* @var EntityInterface $entity */
            $entity->setRid($record->getRid());

            $oData = $record->getOData();
            if (!empty($oData) && is_array($oData)) {
                foreach ($oData as $key => $value) {
                    $entity->{$key} = $value;
                }
            }
            return $entity;
        }

        throw new Exception('Key not found');
    }

}