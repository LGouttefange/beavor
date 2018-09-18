<?php


use Beavor\Dto\Partners\Eureka\PaymentSchedule;
use Beavor\Objify;
use Helper\DummyClass;
use Helper\DummyClassCollection;

class ObjifyCest
{
    const DUMMY_VALUE = 'dummyValue';


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
        $result = (new Objify)->make(new DummyClass(), ['dummyProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
    }

    public function castingRawJsonWorks(UnitTester $I)
    {
        $result = (new Objify)->fromRawJson(new DummyClass(), '{"dummyProperty" : "' . self::DUMMY_VALUE . '"}');
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
    }


    public function castingRawXmlWorks(UnitTester $I)
    {
        $result = (new Objify)->fromRawXml(new DummyClass(), '<?xml version="1.0"?><Root><dummyProperty>'. self::DUMMY_VALUE .'</dummyProperty></Root>');
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
    }

    public function castingSoapXmlWorks(UnitTester $I)
    {
        /** @var PaymentSchedule $result */
        $result = (new Objify)->fromRawXml(PaymentSchedule::class,  file_get_contents(__DIR__."/../_data/GetPaymentScheduleResponse.xml"));
        $I->assertEquals($result->getContent()->getAmount(), 50000);
    }

    public function castingNestedXmlWorks(UnitTester $I)
    {
        $result = (new Objify)->fromRawXml(new DummyClass(), '<?xml version="1.0"?><Root><dummyProperty>dummyValue</dummyProperty><nestedProperty><dummyProperty>dummyValue</dummyProperty></nestedProperty></Root>');
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
        $I->assertEquals($result->nestedProperty->dummyProperty, self::DUMMY_VALUE);
    }


    public function castingStdClassWorks(UnitTester $I)
    {
        $data = new stdClass();
        $data->dummyProperty = self::DUMMY_VALUE;
        $result = (new Objify)->make(new DummyClass(), $data);
        $I->assertEquals($result->dummyProperty, self::DUMMY_VALUE);
    }

    public function castingWithSetterWorks(UnitTester $I)
    {
        $result = (new Objify)->make(new DummyClass(), ['dummySetterProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->getDummySetterProperty(), self::DUMMY_VALUE);
    }

    public function castingWithClassNameWorks(UnitTester $I)
    {
        $result = (new Objify)->make(DummyClass::class, ['dummySetterProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->getDummySetterProperty(), self::DUMMY_VALUE);
    }

    public function castingWithProtectedPropertyDoesNotCrash(UnitTester $I)
    {
        $result = (new Objify)->make(DummyClass::class, ['unaccessibleProperty' => self::DUMMY_VALUE]);
        $I->assertEquals($result->getUnaccessibleProperty(), null);
    }

    public function castingWithUnknownPropertyDoesNothing(UnitTester $I)
    {
        $result = (new Objify)->make(DummyClass::class, ['unknownProperty' => self::DUMMY_VALUE]);
        $I->assertFalse(property_exists($result, 'unknownProperty'));
    }

    public function arrayableTraitWorksAsIntended(UnitTester $I)
    {
        $dummyClass = new DummyClass;
        $dummyClass->setDummySetterProperty('oui');
        $dummyArray = $dummyClass->toArray();

        $I->assertTrue(is_array($dummyArray));
        $I->assertEquals($dummyArray['dummySetterProperty'], 'oui');
    }

    public function nestedPropertyIsCorrectlyCast(UnitTester $I)
    {
        /** @var DummyClass $result */
        $result = (new Objify)->make(DummyClass::class, ['nestedProperty' => ['dummySetterProperty' => self::DUMMY_VALUE]]);
        $I->assertEquals($result->nestedProperty->getDummySetterProperty(), self::DUMMY_VALUE);
    }

    public function nestedSetterPropertyIsCorrectlyCast(UnitTester $I)
    {
        /** @var DummyClass $result */
        $result = (new Objify)->make(DummyClass::class, ['nestedSetterProperty' => ['dummySetterProperty' => self::DUMMY_VALUE]]);
        $I->assertEquals($result->getNestedSetterProperty()->getDummySetterProperty(), self::DUMMY_VALUE);
    }

    public function nestedSetterPropertyWithClassOnPropertyDocIsCorrectlyCast(UnitTester $I)
    {
        /** @var DummyClass $result */
        $result = (new Objify)->make(DummyClass::class, ['nestedSetterDocProperty' => ['dummySetterProperty' => self::DUMMY_VALUE]]);
        $I->assertEquals($result->getNestedSetterDocProperty()->getDummySetterProperty(), self::DUMMY_VALUE);
    }

    public function rootCollectionCreatesChildElements(UnitTester $I)
    {
        /** @var DummyClassCollection $result */
        $result = (new Objify)->make(DummyClassCollection::class, [[['dummyProperty' => self::DUMMY_VALUE]], ['dummyProperty' => self::DUMMY_VALUE]]);
        $I->assertCount(2, $result->objects);
        $I->assertTrue($result->objects[0] instanceof DummyClass);
    }
}
