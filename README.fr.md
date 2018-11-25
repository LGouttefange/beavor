

# Beavor
> _Parce qu'ils voulaient pas que que je l'appelle PerCaster_

## Beavor ça fait quoi

Ca permet d'éviter ça
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

En castant un retour d'API (ou n'importe quel array / stdClass en fait) en un DTO réalisé juste avant.

```php
<?php 

$cconnectObject = (new \Beavor\Objify)->make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```


### Avantages

+ Pas de manipulations de tableaux / d'objets
    +   Si la propriété existe dans le DTO, sa valeur sera définie
    +   Le DTO ne reçoit pas de propriétés supplémentaires
    +   Fini les _undefined index '...'_ 
+ Définition unique des retours d'API
+ Gain de clarté
+ Manipulation OO possibles 
+ Fonctionne avec des champs publics ou avec des setters
+ Possibilité de fonctionner avec des accesseurs/mutateurs

## Utilisation

Pour rappel : un exemple d'usage tout simple :

```php
<?php 

$cconnectObject = (new \Beavor\Objify)->make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Ou on peut appeler statiquement (dans les TODO : gérer avec PHPdoc ou autre un moyen de pas afficher le warning) :
```php
<?php 

$cconnectObject = \Beavor\Objify::make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

En passant une nouvelle instance de la classe DTO :

```php
<?php 

$cconnectObject = \Beavor\Objify::make(new CconnectUserDto(), $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Ou bien la classe même (auquel cas assurez vous d'avoir un constructeur optionnel) :

```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

En passant un tableau :

```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, ['someData' => 'someValue']);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Ou un objet :

```php
<?php 
$cconnectUser = json_decode('<<le JSON>>');
$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Ou même le JSON brut :

Ou un objet :

```php
<?php 

$cconnectObject = (new \Beavor\Objify)->fromRawJson( CconnectUserDto::class, '<<le JSON>>');
$cconnectObject->getGuid();
$cconnectObject->getLastName();
...

```

Un DTO peut contenir d'autres DTO :


```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getAddress(); // instance de CconnectUserAddressDto
$cconnectObject->getAddress()->getCity();
...

```

Si le DTO enfant n'est pas défini dans la classe, alors on a un stdClass :

```php
<?php 

$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);
$cconnectObject->getGuid();
$cconnectObject->getAddress(); // instance de stdClass
$cconnectObject->getAddress()->city;
...

```

Pour définir le DTO enfant, utilisez l'annotation de PhpDoc sur le champ concerné :


```php
<?php

namespace Helper;

class CconnectUserDto
{
    ...
    
    /** @var Address */
    public $Adress;

```

Ca fonctionne aussi avec les collections d'objet :


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

foreach ($users as $user) { // $user est une instance de User
    $user->getName();
}

```

## Le DTO


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

1. Le caster utilise en priorité les setters (ex: le _dummySetterProperty_)
2. Cependant vous pouvez définir la propriété publique et ne pas définir de setter (ex: le _dummyProperty_)
3. Si la propriété est protégée et sans setter, elle ne sera jamais touchée (ex: le _unaccessibleProperty_)
4. Lors du casting aucune propriété n'est rajoutée au DTO. Il n'a que ce qui lui est défini


# Generation de DTO

Si vous avez beaucoup de champs dans votre DTO, que vous avez beaucoup de classes, ou les deux, utilisez le script de génération de DTO !

```php vendor/bin/beavor.php```

On vous demandera :
1. Le nom de classe (ex: CniUploadResponseDto)
2. Le namespace (ex: \Beavor\Dto) 
3. Le JSON minifié de la réponse à transformer

Les fichiers seront automatiquement générés dans votre arborescence directement, avec une détection des racines PSR-4 pour que vous n'ayez plus rien à toucher.

Par défaut, tous les Dto sont générés avec des getters et des champs publics.
