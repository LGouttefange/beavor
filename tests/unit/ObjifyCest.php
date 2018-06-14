<?php


use Beavor\Objify;
use Helper\DummyClass;

class ObjifyCest
{
    const DUMMY_VALUE = 'dummyValue';

    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function staticCallWorks(UnitTester $I)
    {
        $result = Objify::make(new DummyClass(), new stdClass());
        $I->assertInstanceOf(DummyClass::class, $result);
    }

    public function nonStaticCallWorks(UnitTester $I)
    {
        $result = (new Objify)->make(new DummyClass(), new stdClass());
        $I->assertInstanceOf(DummyClass::class, $result);
    }

    public function castingArrayWorks(UnitTester $I)
    {
        $result = Objify::make(new DummyClass(), ['dummyProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
    }

    public function castingStdClassWorks(UnitTester $I)
    {
        $data = new stdClass();
        $data->dummyProperty = self::DUMMY_VALUE;
        $result = Objify::make(new DummyClass(), $data);
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
    }

    public function castingWithSetterWorks(UnitTester $I)
    {
        $result = Objify::make(new DummyClass(), ['dummySetterProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->getDummySetterProperty(), 'DUMMYVALUE');
    }

    public function castingWithClassNameWorks(UnitTester $I)
    {
        $result = Objify::make(DummyClass::class, ['dummySetterProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->getDummySetterProperty(), self::DUMMY_VALUE);
    }

    public function castingWithProtectedPropertyDoesNotCrash(UnitTester $I)
    {
        $result = Objify::make(DummyClass::class, ['unaccessibleProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->getUnaccessibleProperty(), null);
    }

    public function castingWithUnknownPropertyDoesNothing(UnitTester $I)
    {
        $result = Objify::make(DummyClass::class, ['unknownProperty' => self::DUMMY_VALUE]);
        $I->assertFalse(property_exists($result, 'unknownProperty'));
    }

    public function arrayableTraitWorksAsIntended(UnitTester $I)
    {
        $dummyClass = new DummyClass;
        $dummyClass->setDummySetterProperty('oui');
        $dummyArray = $dummyClass->toArray();

        $I->assertTrue(is_array($dummyArray));
        $I->assertEquals($dummyArray['dummyProperty']);
    }
}
