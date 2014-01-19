<?php
namespace PHPUnit\Runner\CleverAndSmart\Unit;

use PHPUnit\Runner\CleverAndSmart\Util;
use PHPUnit_Framework_TestCase as TestCase;

class Mother
{
    public $publicProperty = 'public';

    private $privateProperty = 'private';

    public function getPrivateProperty()
    {
        return $this->privateProperty;
    }

    public function setPrivateProperty($value)
    {
        $this->privateProperty = $value;
    }

    public function getPublicProperty()
    {
        return $this->publicProperty;
    }

    public function setPublicProperty($value)
    {
        $this->publicProperty = $value;
    }
}

class Child extends Mother
{
    protected $protectedProperty = 'protected';

    public function getProtectedProperty()
    {
        return $this->protectedProperty;
    }

    public function setProtectedProperty($value)
    {
        $this->protectedProperty = $value;
    }
}

class UtilTest extends TestCase
{
    public function testCreateRunId()
    {
        $idOne = Util::createRunId();
        $idTwo = Util::createRunId();

        $this->assertSame(128, strlen($idOne));
        $this->assertSame(128, strlen($idTwo));
        $this->assertNotSame($idOne, $idTwo);
    }

    public function testGetInvisiblePropertyByMethod()
    {
        $mother = new Mother();
        $child = new Child();

        $this->assertSame('private', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPrivateProperty'));
        $this->assertSame('private', Util::getInvisibleProperty($child, 'invalidProperty', 'getPrivateProperty'));
        $this->assertSame('protected', Util::getInvisibleProperty($child, 'invalidProperty', 'getProtectedProperty'));
        $this->assertSame('public', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPublicProperty'));
        $this->assertSame('public', Util::getInvisibleProperty($child, 'invalidProperty', 'getPublicProperty'));
    }

    public function testGetInvisiblePropertyByProperty()
    {
        $mother = new Mother();
        $child = new Child();

        $this->assertSame('private', Util::getInvisibleProperty($mother, 'privateProperty'));
        $this->assertSame('private', Util::getInvisibleProperty($mother, 'privateProperty', 'invalidMethod'));
        $this->assertSame('private', Util::getInvisibleProperty($child, 'privateProperty'));
        $this->assertSame('private', Util::getInvisibleProperty($child, 'privateProperty', 'invalidMethod'));
        $this->assertSame('protected', Util::getInvisibleProperty($child, 'protectedProperty'));
        $this->assertSame('protected', Util::getInvisibleProperty($child, 'protectedProperty', 'invalidMethod'));
        $this->assertSame('public', Util::getInvisibleProperty($mother, 'publicProperty'));
        $this->assertSame('public', Util::getInvisibleProperty($mother, 'publicProperty', 'invalidMethod'));
        $this->assertSame('public', Util::getInvisibleProperty($child, 'publicProperty'));
        $this->assertSame('public', Util::getInvisibleProperty($child, 'publicProperty', 'invalidMethod'));
    }

    public function testSetInvisiblePropertyByProperty()
    {
        $mother = new Mother();
        $child = new Child();

        Util::setInvisibleProperty($mother, 'privateProperty', 'private2');
        $this->assertSame('private2', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPrivateProperty'));

        Util::setInvisibleProperty($mother, 'privateProperty', 'private3', 'invalidMethod');
        $this->assertSame('private3', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPrivateProperty'));

        Util::setInvisibleProperty($child, 'privateProperty', 'private2');
        $this->assertSame('private2', Util::getInvisibleProperty($child, 'invalidProperty', 'getPrivateProperty'));

        Util::setInvisibleProperty($child, 'privateProperty', 'private3', 'invalidMethod');
        $this->assertSame('private3', Util::getInvisibleProperty($child, 'invalidProperty', 'getPrivateProperty'));

        Util::setInvisibleProperty($child, 'protectedProperty', 'protected2');
        $this->assertSame('protected2', Util::getInvisibleProperty($child, 'invalidProperty', 'getProtectedProperty'));

        Util::setInvisibleProperty($child, 'protectedProperty', 'protected3', 'invalidMethod');
        $this->assertSame('protected3', Util::getInvisibleProperty($child, 'invalidProperty', 'getProtectedProperty'));

        Util::setInvisibleProperty($mother, 'publicProperty', 'public2');
        $this->assertSame('public2', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPublicProperty'));

        Util::setInvisibleProperty($mother, 'publicProperty', 'public3', 'methodName');
        $this->assertSame('public3', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPublicProperty'));

        Util::setInvisibleProperty($child, 'publicProperty', 'public2');
        $this->assertSame('public2', Util::getInvisibleProperty($child, 'invalidProperty', 'getPublicProperty'));

        Util::setInvisibleProperty($child, 'publicProperty', 'public3', 'methodName');
        $this->assertSame('public3', Util::getInvisibleProperty($child, 'invalidProperty', 'getPublicProperty'));
    }

    public function testSetInvisiblePropertyByMethod()
    {
        $mother = new Mother();
        $child = new Child();

        Util::setInvisibleProperty($mother, 'invalidProperty', 'private2', 'setPrivateProperty');
        $this->assertSame('private2', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPrivateProperty'));

        Util::setInvisibleProperty($child, 'invalidProperty', 'private2', 'setPrivateProperty');
        $this->assertSame('private2', Util::getInvisibleProperty($child, 'invalidProperty', 'getPrivateProperty'));

        Util::setInvisibleProperty($child, 'invalidProperty', 'protected2', 'setProtectedProperty');
        $this->assertSame('protected2', Util::getInvisibleProperty($child, 'invalidProperty', 'getProtectedProperty'));

        Util::setInvisibleProperty($mother, 'invalidProperty', 'public2', 'setPublicProperty');
        $this->assertSame('public2', Util::getInvisibleProperty($mother, 'invalidProperty', 'getPublicProperty'));

        Util::setInvisibleProperty($child, 'invalidProperty', 'public2', 'setPublicProperty');
        $this->assertSame('public2', Util::getInvisibleProperty($child, 'invalidProperty', 'getPublicProperty'));
    }

    public function testGetInvalidProperty()
    {
        $this->setExpectedException(
            'PHPUnit\Runner\CleverAndSmart\Exception\PropertyReflectionException',
            'Property "invalidProperty" does not exist in hierarchy PHPUnit\Runner\CleverAndSmart\Unit\Child < PHPUnit\Runner\CleverAndSmart\Unit\Mother'
        );
        Util::getInvisibleProperty(new Child(), 'invalidProperty');
    }

    public function testSetInvalidProperty()
    {
        $this->setExpectedException(
            'PHPUnit\Runner\CleverAndSmart\Exception\PropertyReflectionException',
            'Property "invalidProperty" does not exist in hierarchy PHPUnit\Runner\CleverAndSmart\Unit\Child < PHPUnit\Runner\CleverAndSmart\Unit\Mother'
        );
        Util::setInvisibleProperty(new Child(), 'invalidProperty', 'value');
    }
}
