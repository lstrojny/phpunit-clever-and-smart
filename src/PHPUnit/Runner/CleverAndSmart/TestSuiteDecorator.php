<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestResult as TestResult;
use PHPUnit_Framework_TestSuite as TestSuite;
use PHPUnit_Framework_Test as Test;

class TestSuiteDecorator extends TestSuite
{
    private $wrapped;

    public function __construct(TestSuite $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * @param TestSuite $suite
     * @return TestSuiteDecorator
     */
    public static function decorate(TestSuite $suite)
    {
        return $suite instanceof self ? $suite : new self($suite);
    }

    public function setTests(array $tests)
    {
        $this->wrapped->tests = $tests;
    }

    public function getGroupDetails()
    {
        return $this->wrapped->groups;
    }

    public function setGroupDetails(array $groups)
    {
        $this->wrapped->groups = $groups;
    }

    public function toString()
    {
        return $this->getName();
    }

    public function addTest(Test $test, $groups = [])
    {
        $this->wrapped->addTest($test, $groups);
    }

    public function addTestSuite($testClass)
    {
        $this->wrapped->addTestSuite($testClass);
    }

    public function addTestFile($filename, $phptOptions = array())
    {
        $this->wrapped->addTestFile($filename, $phptOptions);
    }

    public function addTestFiles($filenames)
    {
        $this->wrapped->addTestFiles($filenames);
    }

    public function count()
    {
        return $this->wrapped->count();
    }

    public function getName()
    {
        return $this->wrapped->getName();
    }

    public function getGroups()
    {
        return $this->wrapped->getGroups();
    }

    public function run(TestResult $result = NULL, $filter = FALSE, array $groups = array(), array $excludeGroups = array(), $processIsolation = FALSE)
    {
        return $this->wrapped->run($result, $filter, $groups, $excludeGroups, $processIsolation);
    }

    public function runTest(Test $test, TestResult $result)
    {
        $this->wrapped->runTest($test, $result);
    }

    public function setName($name)
    {
        $this->wrapped->setName($name);
    }

    public function testAt($index)
    {
        return $this->wrapped->testAt($index);
    }

    public function tests()
    {
        return $this->wrapped->tests();
    }

    public function markTestSuiteSkipped($message = '')
    {
        $this->wrapped->markTestSuiteSkipped($message);
    }

    public function setBackupGlobals($backupGlobals)
    {
        $this->wrapped->setBackupGlobals($backupGlobals);
    }

    public function setBackupStaticAttributes($backupStaticAttributes)
    {
        $this->wrapped->setBackupStaticAttributes($backupStaticAttributes);
    }

    public function getIterator()
    {
        return $this->wrapped->getIterator();
    }
}
