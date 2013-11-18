<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class IntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->reset();
    }

    public function tearDown()
    {
        $this->reset();
    }

    private function reset()
    {
        $file = __DIR__ . '/../.phpunit-cas.db';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function testSimpleCase()
    {
        $this->runTests('SimpleTest', 'failure', 'failure', false);
        $this->runTests('SimpleTest', 'success', 'success', true);
        $this->runTests('SimpleTest', 'success', 'retry', true);

        $this->assertEquals(3, $this->getIntegrationTestSuiteAttribute('failure', 'tests'));
        $this->assertEquals(1, $this->getIntegrationTestSuiteAttribute('failure', 'failures'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('failure', 'errors'));
        $this->assertSame('testSuccess', $this->getIntegrationTestCaseAttribute('failure', 0, 'name'));
        $this->assertSame('testFailure', $this->getIntegrationTestCaseAttribute('failure', 1, 'name'));
        $this->assertSame('testError', $this->getIntegrationTestCaseAttribute('failure', 2, 'name'));

        $this->assertEquals(3, $this->getIntegrationTestSuiteAttribute('success', 'tests'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('success', 'failures'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('success', 'errors'));
        $this->assertSame('testFailure', $this->getIntegrationTestCaseAttribute('success', 0, 'name'));
        $this->assertSame('testSuccess', $this->getIntegrationTestCaseAttribute('success', 1, 'name'));
        $this->assertSame('testError', $this->getIntegrationTestCaseAttribute('success', 2, 'name'));

        $this->assertEquals(3, $this->getIntegrationTestSuiteAttribute('retry', 'tests'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('retry', 'failures'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('retry', 'errors'));
        $this->assertSame('testFailure', $this->getIntegrationTestCaseAttribute('retry', 0, 'name'));
        $this->assertSame('testSuccess', $this->getIntegrationTestCaseAttribute('retry', 1, 'name'));
        $this->assertSame('testError', $this->getIntegrationTestCaseAttribute('retry', 2, 'name'));
    }

    public function testSimpleCaseGrouped()
    {
        $this->runTests('SimpleTest', 'failure', 'failure', false, 'grp');
        $this->runTests('SimpleTest', 'success', 'success', true, 'grp');
        $this->runTests('SimpleTest', 'success', 'retry', true, 'grp');

        $this->assertEquals(3, $this->getIntegrationTestSuiteAttribute('failure', 'tests'));
        $this->assertEquals(1, $this->getIntegrationTestSuiteAttribute('failure', 'failures'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('failure', 'errors'));
        $this->assertSame('testSuccess', $this->getIntegrationTestCaseAttribute('failure', 0, 'name'));
        $this->assertSame('testFailure', $this->getIntegrationTestCaseAttribute('failure', 1, 'name'));
        $this->assertSame('testError', $this->getIntegrationTestCaseAttribute('failure', 2, 'name'));

        $this->assertEquals(3, $this->getIntegrationTestSuiteAttribute('success', 'tests'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('success', 'failures'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('success', 'errors'));
        $this->assertSame('testFailure', $this->getIntegrationTestCaseAttribute('success', 0, 'name'));
        $this->assertSame('testSuccess', $this->getIntegrationTestCaseAttribute('success', 1, 'name'));
        $this->assertSame('testError', $this->getIntegrationTestCaseAttribute('success', 2, 'name'));

        $this->assertEquals(3, $this->getIntegrationTestSuiteAttribute('retry', 'tests'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('retry', 'failures'));
        $this->assertEquals(0, $this->getIntegrationTestSuiteAttribute('retry', 'errors'));
        $this->assertSame('testFailure', $this->getIntegrationTestCaseAttribute('retry', 0, 'name'));
        $this->assertSame('testSuccess', $this->getIntegrationTestCaseAttribute('retry', 1, 'name'));
        $this->assertSame('testError', $this->getIntegrationTestCaseAttribute('retry', 2, 'name'));
    }

    private function runTests($testFile, $state, $runName, $expectedResult, $group = null)
    {
        $phpunit = realpath(__DIR__ . '/../../../../../../../vendor/bin/phpunit');
        $commandString = 'php -d error_log=/tmp/dev.log '
            . $phpunit
            . ' --configuration ' . __DIR__ . '/../phpunit-%s.xml'
            . ' --log-junit ' . __DIR__ . '/../result-' . $runName . '.xml'
            . ' --filter ' . $testFile;

        if ($group) {
            $commandString .= ' --group ' . $group;
        }

        $process = new Process(sprintf($commandString, $state));
        $process->setWorkingDirectory(__DIR__ . '/../');
        $process->run();

        $this->assertTrue(
            $expectedResult ? $process->isSuccessful() : !$process->isSuccessful(),
            $process->getOutput() . "\n\n\n" . $process->getErrorOutput()
        );
    }

    private function getIntegrationTestResult($runName)
    {
        return simplexml_load_file(__DIR__ . '/../result-' . $runName . '.xml');
    }

    private function getIntegrationTestSuiteAttribute($state, $attribute)
    {
        return (string) $this->getIntegrationTestResult($state)->testsuite[$attribute];
    }

    private function getIntegrationTestCaseAttribute($state, $index, $attribute)
    {
        return (string) $this->getIntegrationTestResult($state)->testsuite->testsuite->testcase[$index][$attribute];
    }
}
