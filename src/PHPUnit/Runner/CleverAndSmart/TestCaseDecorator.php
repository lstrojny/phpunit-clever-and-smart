<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestResult as TestResult;

class TestCaseDecorator extends TestCase
{
    private $wrapped;

    public function __construct(TestCase $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * @param TestCase $testCase
     * @return TestCaseDecorator
     */
    public static function decorate(TestCase $testCase)
    {
        return $testCase instanceof self ? $testCase : new self($testCase);
    }

    public function hasDependencies()
    {
        return count($this->wrapped->dependencies) > 0;
    }

    public function toString()
    {
        return $this->wrapped->toString();
    }

    public function count()
    {
        return $this->wrapped->count();
    }

    public function getAnnotations()
    {
        return $this->wrapped->getAnnotations();
    }

    public function getName($withDataSet = true)
    {
        return $this->wrapped->getName($withDataSet);
    }

    public function getSize()
    {
        return $this->wrapped->getSize();
    }

    public function getActualOutput()
    {
        return $this->wrapped->getActualOutput();
    }

    public function hasOutput()
    {
        return $this->wrapped->hasOutput();
    }

    public function expectOutputRegex($expectedRegex)
    {
        $this->wrapped->expectOutputRegex($expectedRegex);
    }

    public function expectOutputString($expectedString)
    {
        $this->wrapped->expectOutputString($expectedString);
    }

    public function hasPerformedExpectationsOnOutput()
    {
        return $this->wrapped->hasPerformedExpectationsOnOutput();
    }

    public function getExpectedException()
    {
        return $this->wrapped->getExpectedException();
    }

    public function setExpectedException($exceptionName, $exceptionMessage = '', $exceptionCode = null)
    {
        $this->wrapped->setExpectedException($exceptionName, $exceptionMessage, $exceptionCode);
    }

    public function setUseErrorHandler($useErrorHandler)
    {
        $this->wrapped->setUseErrorHandler($useErrorHandler);
    }

    public function setUseOutputBuffering($useOutputBuffering)
    {
        $this->wrapped->setUseOutputBuffering($useOutputBuffering);
    }

    public function getStatus()
    {
        return $this->wrapped->getStatus();
    }

    public function getStatusMessage()
    {
        return $this->wrapped->getStatusMessage();
    }

    public function hasFailed()
    {
        return $this->wrapped->hasFailed();
    }

    public function run(TestResult $result = null)
    {
        return $this->wrapped->run($result);
    }

    public function runBare()
    {
        $this->wrapped->runBare();
    }

    public function setName($name)
    {
        $this->wrapped->setName($name);
    }

    public function setDependencies(array $dependencies)
    {
        $this->wrapped->setDependencies($dependencies);
    }

    public function setDependencyInput(array $dependencyInput)
    {
        $this->wrapped->setDependencyInput($dependencyInput);
    }

    public function setBackupGlobals($backupGlobals)
    {
        $this->wrapped->setBackupGlobals($backupGlobals);
    }

    public function setBackupStaticAttributes($backupStaticAttributes)
    {
        $this->wrapped->setBackupStaticAttributes($backupStaticAttributes);
    }

    public function setRunTestInSeparateProcess($runTestInSeparateProcess)
    {
        $this->wrapped->setRunTestInSeparateProcess($runTestInSeparateProcess);
    }

    public function setPreserveGlobalState($preserveGlobalState)
    {
        $this->wrapped->setPreserveGlobalState($preserveGlobalState);
    }

    public function setInIsolation($inIsolation)
    {
        $this->wrapped->setInIsolation($inIsolation);
    }

    public function getResult()
    {
        return $this->wrapped->getResult();
    }

    public function setResult($result)
    {
        $this->wrapped->setResult($result);
    }

    public function setOutputCallback($callback)
    {
        $this->wrapped->setOutputCallback($callback);
    }

    public function getTestResultObject()
    {
        return $this->wrapped->getTestResultObject();
    }

    public function setTestResultObject(TestResult $result)
    {
        $this->wrapped->setTestResultObject($result);
    }

    public function getMock(
        $originalClassName,
        $methods = [],
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true,
        $cloneArguments = false
    )
    {
        return $this->wrapped->getMock(
            $originalClassName,
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $cloneArguments
        );
    }

    public function getMockBuilder($className)
    {
        return $this->wrapped->getMockBuilder($className);
    }

    public function getMockForAbstractClass(
        $originalClassName,
        array $arguments = [],
        $mockClassName = '',
        $callOriginalConstructor = true,
        $callOriginalClone = true,
        $callAutoload = true,
        $mockedMethods = [],
        $cloneArguments = false
    )
    {
        return $this->wrapped->getMockForAbstractClass(
            $originalClassName,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $mockedMethods,
            $cloneArguments);
    }

    public function addToAssertionCount($count)
    {
        $this->wrapped->addToAssertionCount($count);
    }

    public function getNumAssertions()
    {
        return $this->wrapped->getNumAssertions();
    }
}
