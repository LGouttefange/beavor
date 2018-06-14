# Beavor
> _Parce qu'ils voulaient pas que que je l'appelle PerCaster_

## Objify ça fait quoi

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

### Inconvénients

+ Les DTO imbriqués ne sont pas encore gérés
+ Devoir créer soi-même les classes DTO 

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

### Petit plus

Un trait Beavor\Helpers\Arrayable permet de transformer le DTO en tableau (mais il est optionnel) :


```php
<?php 
$cconnectUser = json_decode('<<le JSON>>');
$cconnectObject = \Beavor\Objify::make( Beavor\Dto\CconnectUserDto::class, $cconnectUser);

$cconnectArray = $cconnectObject->toArray();
...
$firstName = $cconnectArray['FirstName'];

```
Attention : n'utilise pas encore les Getters pour générer le tableau

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

## Les TODO 

1. Le nested casting avec l'utilisation des PhpDoc pour le type hinting
2. Tout ce qui est nested response en général ?
3. Un générateur de classe DTO à partir d'un JSON