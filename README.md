OrientDb entity manager
=============================

Install
```code
composer require spartaksun/orientdb-entity
```
Example of services.yml:
```yml 
services:
    orient:
        class: PhpOrient\PhpOrient
        public: false
        properties:
            hostname: 'localhost'
            port: 2424
            username: 'root'
            password: 'root'
    orient.em:
        class: spartaksun\OrientDb\EntityManager
        arguments: [@orient, "your_orient_db_name"]
        properties:
            classMap:
                "Country": YourBundle\Entity\Country
                "City": YourBundle\Entity\Country
```

Define entities by extending spartaksun\OrientDb\Entity class. 
Use internal validators or define your own by extending abstract spartaksun\OrientDb\Validators\Validator:
```php
/**
 * User entity
 * @property $first_name
 * @property $last_name
 */
class User extends spartaksun\OrientDb\Entity
{
    /**
     * {@inheritdoc}
     */
    public function validators()
    {
        return [
            'first_name' => [
                [
                    spartaksun\OrientDb\Validators\StringValidator::class, 
                    ['min' => 3, 'max' => 32],
                ],
            ],
            'last_name' => [
                [
                    spartaksun\OrientDb\Validators\StringValidator::class, 
                    ['min' => 5, 'max' => 50],
                ],
            ],
        ];
    }
}
```


Usage in Symfony2 controller:
```php
$this->get('orient.em');
// Init repository
$repository = $this->get('orient.em')
        ->getRepository( Country::class );
// Get all countries
$countries = $repository->findAll();
foreach($countries as $country) {
     echo $country->name . "\n";
}
// Add new country
$country = new Country();
$country->name = 'Ukraine';
if($repository->persist($country)) {
    $rid = $country->getRid();
} else {
    var_dump($country->getErrors());
}
// find one
$country = $repository->find('name=?', 'Ukraine')
```
    
