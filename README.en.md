# Beavor

## Okay, so what about Beavor ?

It prevents these scenarii :
```php
<?php 

return [
    'guid' => $cconnectUser['CustomerGuid'],
    'civility' => $cconnectUser['Civility'],
    'firstname' => $cconnectUser['FirstName'],
    'lastname' => $cconnectUser['LastName'],
    'maidenname' => !empty($cconnectUser['MaidenName']) ? $cconnectUser['MaidenName'] : null,
    'birthdate' => !empty($cconnectUser['Birthdate']) ? $cconnectUser['Birthdate'] : '1970-01-01',
    'email' => $cconnectUser['Email'],
    'birthDepartment' => isset($cconnectUser['BirthDepartment']) ? $cconnectUser['BirthDepartment'] : null,
    'phoneNumber' => !empty($cconnectUser['PhoneNumber']) ? $cconnectUser['PhoneNumber'] : null,
    'cellPhoneNumber' => !empty($cconnectUser['MobilePhoneNumber']) ? $cconnectUser['MobilePhoneNumber'] : null,
];
```

By casting an API return (or any array/stdclass as a matter of fact), into a DTO generated beforehand.

```php
<?php 

$cconnectObject = (new \Beavor\Objify)->make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```


### The pros

+ No stdClass/array primitive manipulation
    +   Default values, bulk edit with the entire array
    +   DTOs do not recieve additional data
    +   Gotta stop the _undefined index '...'_ errors 
+ API return have a code equivalent in your cade, increasing lisibility
+ Gain de clarté
+ OO manipulations  
+ Use setters or public properties
+ Work with accessors/mutators

## Usage

Let's start simple :

```php
<?php 

$cconnectObject = (new \Beavor\Objify)->make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Or let's start static (TODO : find a way to prevent warning. I swear it doesn't cause an error) :
```php
<?php 

$cconnectObject = \Beavor\Objify::makeStatic(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

You can pass a new class instance :

```php
<?php 

$cconnectObject = \Beavor\Objify::make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Or the class itself (be sure to have an optional constructor) :

```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Pass values within an array :

```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, ['someData' => 'someValue']);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Or an object :

```php
<?php 
$cconnectUser = json_decode('<<le JSON>>');
$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Event the raw JSON :

```php
<?php 

$cconnectObject = (new \Beavor\Objify)->fromRawJson( CconnectUserDto::class, '<<le JSON>>');
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

A DTO can contain other DTOs :


```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getAddress(); // instance of CconnectUserAddressDto
$cconnectObject->getAddress()->getCity();
...

```

Si the child's class is not defined within the DTO, it'll be a stdClass :

```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getAddress(); // instance de stdClass
$cconnectObject->getAddress()->city;
...

```

To define the child, simply put the class in the PhpDoc block :


```php
<?php

namespace Helper;

class CconnectUserDto
{
    ...
    
    /** @var Address */
    public $Adress;

```

Also works with objects collection :


```php
<?php

namespace Helper;

class GetUsersDto
{
    ...
    
    /** @var User[] */
    public $users;

```


```php
<?php 

$cconnectObject = (new \Beavor\Objify)->make( GetUsersDto::class, $response);

foreach ($users as $user) { // $user is an instance  of User
    $user->getName();
}

```

## The DTO


Exemple de DTO :

```php
<?php


namespace Helper;


class DummyClass
{
    use Beavor\Helpers\Arrayable;
    
    public $dummyProperty;
    protected $dummySetterProperty;
    private $unaccessibleProperty;
    /** @var DummyClass */
    public $nestedProperty;

    /**
     * @return mixed
     */
    public function getDummySetterProperty()
    {
        return $this->dummySetterProperty;
    }

    /**
     * @param mixed $dummySetterProperty
     */
    public function setDummySetterProperty($dummySetterProperty)
    {
        $this->dummySetterProperty = mb_strtoupper($dummySetterProperty);
    }

    /**
     * @return mixed
     */
    public function getUnaccessibleProperty()
    {
        return $this->unaccessibleProperty;
    }
}
```

1. The caster will first try to use the setter (ex:  _dummySetterProperty_)
2. But you can use public properties without setter for a thinner class (ex:  _dummyProperty_)
3. A protected property without accessor will never be set (ex:  _unaccessibleProperty_)
4. No attribute is added. Only properties the DTO knows are filled


# DTO Generation

Manually creating DTOs can be a hassle, I know. That's why a script is bundled !

```php vendor/bin/beavor.php```

You will be asked :
1. The className (ex: CniUploadResponseDto)
2. The namespace (ex: \Beavor\Dto) 
3. The minified XML/JSON to be structured into a DTO

Files will be directly created in your project tree, with an automatic PSR-4 root detection, so your DTOs are directly usable.

By defaults, DTOs have public properties with their associated getter.
