<?php

require "../vendor/autoload.php";

// Define entity and validators in it

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
                    spartaksun\OrientDb\Validators\StringValidator::class, ['min' => 3, 'max' => 32 ],
                ],
            ],
            'last_name' => [
                [
                    spartaksun\OrientDb\Validators\StringValidator::class, ['min' => 3, 'max' => 32 ],
                ],
            ],
        ];
    }
}


// Define OrientDb client

$client = new \PhpOrient\PhpOrient();
$client->hostname = 'localhost';
$client->port = 2424;
$client->username = 'root';
$client->password = 'root';

// Initialize entity manager
$manager = new \spartaksun\OrientDb\EntityManager($client, 'your_orientdb_name');

// Set mapping of OrientDb classes to your PHP classes
$manager->classMap = [
    "User" =>  User::class
];


// Initialize repository
$repository = $manager->getRepository(User::class);


// Get array of User entities
$users = $repository->findAll(20 /* limit */);
/* @var $users User[] */

// Change some of them and persist changes
if(!empty($users)) {
    foreach($users as $user) {
        $user->last_name = 'Test Last Name';
        $repository->persist($user);
    }
}


// Find one
$user = $repository->find('name=?', 'Fillip');
if(!empty($user)) { /* @var $user User */
    echo $user->last_name . " " . $user->first_name . PHP_EOL; // i.e. Morris Fillip
}

// Insert one record
$user = new User();
$user->first_name = 'Dan';
$user->last_name = 'Brown';
$success = $repository->persist($user);


// Get entity errors
if(!$success) {
    foreach($user->getErrors() as $attribute => $errors) {
        foreach($errors as $errorText) {
            echo "Error in {$attribute}:" . $errorText . PHP_EOL; // i.e. first_name is too long (maximum is 32 characters).
        }
    }
}
