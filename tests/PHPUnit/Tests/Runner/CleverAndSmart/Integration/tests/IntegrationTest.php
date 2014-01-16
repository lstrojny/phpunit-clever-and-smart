<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;
use SimpleXMLElement;
use Symfony\Component\Process\Process;

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

    public function testSimpleCase_FailuresOnly()
    {
        $this->runTests('SimpleTest', 'failure', 'failure', false);
        $this->runTests('SimpleTest', 'success', 'success', true);
        $this->runTests('SimpleTest', 'success', 'retry', true);

        $this->assertTestSuitePosition('failure', 'SimpleTest', 5);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'failures', 1);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('failure', 'SimpleTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testFailure', 2);
        $this->assertTestPosition('failure', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('success', 'SimpleTest', 1);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('success', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('success', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('success', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('retry', 'SimpleTest', 1);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('retry', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('retry', 'SimpleTest::testError', 3);
    }

    public function testSimpleCase_ErrorsOnly()
    {
        $this->runTests('SimpleTest', 'error', 'failure', false);
        $this->runTests('SimpleTest', 'success', 'success', true);
        $this->runTests('SimpleTest', 'success', 'retry', true);

        $this->assertTestSuitePosition('failure', 'SimpleTest', 5);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'errors', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testFailure', 2);
        $this->assertTestPosition('failure', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('success', 'SimpleTest', 1);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('success', 'SimpleTest::testError', 1);
        $this->assertTestPosition('success', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('success', 'SimpleTest::testFailure', 3);

        $this->assertTestSuitePosition('retry', 'SimpleTest', 1);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SimpleTest::testError', 1);
        $this->assertTestPosition('retry', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('retry', 'SimpleTest::testFailure', 3);
    }

    public function testSimpleCase_ErrorsAndFailures()
    {
        $this->runTests('SimpleTest', 'error-failure', 'failure', false);
        $this->runTests('SimpleTest', 'success', 'success', true);
        $this->runTests('SimpleTest', 'success', 'retry', true);

        $this->assertTestSuitePosition('failure', 'SimpleTest', 5);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'failures', 1);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'errors', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testFailure', 2);
        $this->assertTestPosition('failure', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('success', 'SimpleTest', 1);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('success', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('success', 'SimpleTest::testError', 2);
        $this->assertTestPosition('success', 'SimpleTest::testSuccess', 3);

        $this->assertTestSuitePosition('retry', 'SimpleTest', 1);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('retry', 'SimpleTest::testError', 2);
        $this->assertTestPosition('retry', 'SimpleTest::testSuccess', 3);
    }

    public function testSimpleCaseGrouped_FailuresOnly()
    {
        $this->runTests('SimpleTest', 'failure', 'failure', false, 'grp');
        $this->runTests('SimpleTest', 'success', 'success', true, 'grp');
        $this->runTests('SimpleTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('failure', 'SimpleTest', 5);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'failures', 1);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('failure', 'SimpleTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testFailure', 2);
        $this->assertTestPosition('failure', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('success', 'SimpleTest', 1);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('success', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('success', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('success', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('retry', 'SimpleTest', 1);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('retry', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('retry', 'SimpleTest::testError', 3);
    }

    public function testSimpleCaseGrouped_ErrorsOnly()
    {
        $this->runTests('SimpleTest', 'error', 'failure', false, 'grp');
        $this->runTests('SimpleTest', 'success', 'success', true, 'grp');
        $this->runTests('SimpleTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('failure', 'SimpleTest', 5);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'errors', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testFailure', 2);
        $this->assertTestPosition('failure', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('success', 'SimpleTest', 1);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('success', 'SimpleTest::testError', 1);
        $this->assertTestPosition('success', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('success', 'SimpleTest::testFailure', 3);

        $this->assertTestSuitePosition('retry', 'SimpleTest', 1);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SimpleTest::testError', 1);
        $this->assertTestPosition('retry', 'SimpleTest::testSuccess', 2);
        $this->assertTestPosition('retry', 'SimpleTest::testFailure', 3);
    }

    public function testSimpleCaseGrouped_ErrorsAndFailures()
    {
        $this->runTests('SimpleTest', 'error-failure', 'failure', false, 'grp');
        $this->runTests('SimpleTest', 'success', 'success', true, 'grp');
        $this->runTests('SimpleTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('failure', 'SimpleTest', 5);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'failures', 1);
        $this->assertTestSuiteResult('failure', 'SimpleTest', 'errors', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'SimpleTest::testFailure', 2);
        $this->assertTestPosition('failure', 'SimpleTest::testError', 3);

        $this->assertTestSuitePosition('success', 'SimpleTest', 1);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('success', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('success', 'SimpleTest::testError', 2);
        $this->assertTestPosition('success', 'SimpleTest::testSuccess', 3);

        $this->assertTestSuitePosition('retry', 'SimpleTest', 1);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SimpleTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SimpleTest::testFailure', 1);
        $this->assertTestPosition('retry', 'SimpleTest::testError', 2);
        $this->assertTestPosition('retry', 'SimpleTest::testSuccess', 3);
    }

    public function testDataProviderTestCase()
    {
        $this->runTests('DataTest', 'error', 'failure', false, 'grp');
        $this->runTests('DataTest', 'success', 'success', true, 'grp');
        $this->runTests('DataTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('failure', 'DataTest', 1);
        $this->assertTestSuiteResult('failure', 'DataTest', 'tests', 3);
        $this->assertTestSuiteResult('failure', 'DataTest', 'failures', 0);
        $this->assertTestSuiteResult('failure', 'DataTest', 'errors', 1);
        $this->assertTestPosition('failure', 'DataTest::testData with data set #0', 1);
        $this->assertTestPosition('failure', 'DataTest::testData with data set #1', 2);
        $this->assertTestPosition('failure', 'DataTest::testData with data set #2', 3);

        $this->assertTestSuitePosition('success', 'DataTest', 1);
        $this->assertTestSuiteResult('success', 'DataTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'DataTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'DataTest', 'errors', 0);
        $this->assertTestPosition('success', 'DataTest::testData with data set #1', 1);
        $this->assertTestPosition('success', 'DataTest::testData with data set #0', 2);
        $this->assertTestPosition('success', 'DataTest::testData with data set #2', 3);

        $this->assertTestSuitePosition('retry', 'DataTest', 1);
        $this->assertTestSuiteResult('retry', 'DataTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'DataTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'DataTest', 'errors', 0);
        $this->assertTestPosition('retry', 'DataTest::testData with data set #1', 1);
        $this->assertTestPosition('retry', 'DataTest::testData with data set #0', 2);
        $this->assertTestPosition('retry', 'DataTest::testData with data set #2', 3);
    }

    public function testDependentTest()
    {
        $this->runTests('DependentTest', 'failure', 'failure', false, 'grp');
        $this->runTests('DependentTest', 'success', 'success', true, 'grp');
        $this->runTests('DependentTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('failure', 'DependentTest', 1);
        $this->assertTestSuiteResult('failure', 'DependentTest', 'tests', 2);
        $this->assertTestSuiteResult('failure', 'DependentTest', 'failures', 1);
        $this->assertTestSuiteResult('failure', 'DependentTest', 'errors', 0);
        $this->assertTestPosition('failure', 'DependentTest::testSuccess', 1);
        $this->assertTestPosition('failure', 'DependentTest::testFailure', 2);

        $this->assertTestSuitePosition('success', 'DependentTest', 1);
        $this->assertTestSuiteResult('success', 'DependentTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'DependentTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'DependentTest', 'errors', 0);
        $this->assertTestPosition('success', 'DependentTest::testSuccess', 1);
        $this->assertTestPosition('success', 'DependentTest::testFailure', 2);
        $this->assertTestPosition('success', 'DependentTest::testError', 3);

        $this->assertTestSuitePosition('retry', 'DependentTest', 1);
        $this->assertTestSuiteResult('retry', 'DependentTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'DependentTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'DependentTest', 'errors', 0);
        $this->assertTestPosition('retry', 'DependentTest::testSuccess', 1);
        $this->assertTestPosition('retry', 'DependentTest::testFailure', 2);
        $this->assertTestPosition('retry', 'DependentTest::testError', 3);
    }

    public function testFatalError()
    {
        $this->runTests('FatalErrorTest', 'fatal', 'fatal', false, 'grp');
        $this->runTests('FatalErrorTest', 'success', 'success', true, 'grp');
        $this->runTests('FatalErrorTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('success', 'FatalErrorTest', 1);
        $this->assertTestSuiteResult('success', 'FatalErrorTest', 'tests', 3);
        $this->assertTestSuiteResult('success', 'FatalErrorTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'FatalErrorTest', 'errors', 0);
        $this->assertTestPosition('success', 'FatalErrorTest::testFatal', 1);
        $this->assertTestPosition('success', 'FatalErrorTest::testSuccess2', 2);
        $this->assertTestPosition('success', 'FatalErrorTest::testSuccess1', 3);

        $this->assertTestSuitePosition('retry', 'FatalErrorTest', 1);
        $this->assertTestSuiteResult('retry', 'FatalErrorTest', 'tests', 3);
        $this->assertTestSuiteResult('retry', 'FatalErrorTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'FatalErrorTest', 'errors', 0);
        $this->assertTestPosition('retry', 'FatalErrorTest::testFatal', 1);
        $this->assertTestPosition('retry', 'FatalErrorTest::testSuccess1', 2);
        $this->assertTestPosition('retry', 'FatalErrorTest::testSuccess2', 3);
    }

    public function testSkippedAndIncompleteTests()
    {
        $this->runTests('SkipTest', 'skip', 'skip', true, 'grp');
        $this->runTests('SkipTest', 'success', 'success', true, 'grp');
        $this->runTests('SkipTest', 'success', 'retry', true, 'grp');

        $this->assertTestSuitePosition('skip', 'SkipTest', 6);
        $this->assertTestSuiteResult('skip', 'SkipTest', 'tests', 2);
        $this->assertTestSuiteResult('skip', 'SkipTest', 'failures', 0);
        $this->assertTestSuiteResult('skip', 'SkipTest', 'errors', 0);
        $this->assertTestPosition('skip', 'SkipTest::testSuccess1', 1);
        $this->assertTestPosition('skip', 'SkipTest::testSuccess2', 2);

        $this->assertTestSuitePosition('success', 'SkipTest', 1);
        $this->assertTestSuiteResult('success', 'SkipTest', 'tests', 4);
        $this->assertTestSuiteResult('success', 'SkipTest', 'failures', 0);
        $this->assertTestSuiteResult('success', 'SkipTest', 'errors', 0);
        $this->assertTestPosition('success', 'SkipTest::testSkipped', 1);
        $this->assertTestPosition('success', 'SkipTest::testIncomplete', 2);
        $this->assertTestPosition('success', 'SkipTest::testSuccess1', 3);
        $this->assertTestPosition('success', 'SkipTest::testSuccess2', 4);

        $this->assertTestSuitePosition('retry', 'SkipTest', 1);
        $this->assertTestSuiteResult('retry', 'SkipTest', 'tests', 4);
        $this->assertTestSuiteResult('retry', 'SkipTest', 'failures', 0);
        $this->assertTestSuiteResult('retry', 'SkipTest', 'errors', 0);
        $this->assertTestPosition('retry', 'SkipTest::testSkipped', 1);
        $this->assertTestPosition('retry', 'SkipTest::testIncomplete', 2);
        $this->assertTestPosition('retry', 'SkipTest::testSuccess1', 3);
        $this->assertTestPosition('retry', 'SkipTest::testSuccess2', 4);
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

    private static function assertTestPosition($runName, $testName, $expectedPosition)
    {
        $resultFilePath = static::getResultFilePath($runName);
        list($class, $method) = explode('::', $testName);
        $expression = sprintf(
            '//testsuite[contains(@name, "%s")]/testcase[%d][@name="%s"]',
            $class,
            $expectedPosition,
            $method
        );

        $xml = file_get_contents($resultFilePath);
        static::assertXpathNotEmpty(
            $xml,
            $expression,
            sprintf('Could not find XPath expression "%s" in "%s" (%s)', $expression, $resultFilePath, $xml)
        );
    }

    private static function assertTestSuitePosition($runName, $suite, $expectedPosition)
    {
        $resultFilePath = static::getResultFilePath($runName);
        $expression = sprintf(
            '//testsuite/testsuite[%d][contains(@name, "%s")]',
            $expectedPosition,
            $suite
        );

        $xml = file_get_contents($resultFilePath);
        static::assertXpathNotEmpty(
            $xml,
            $expression,
            sprintf('Could not find XPath expression "%s" in "%s" (%s)', $expression, $resultFilePath, $xml)
        );
    }

    private static function assertTestSuiteResult($runName, $class, $attribute, $expectedValue)
    {
        $resultFilePath = static::getResultFilePath($runName);
        $expression = sprintf(
            '//testsuite[contains(@name, "%s")]/@%s',
            $class,
            $attribute
        );

        $xml = file_get_contents($resultFilePath);
        static::assertXpathEquals($xml, $expression, $expectedValue);
    }

    private static function assertXpathNotEmpty($xml, $xpath, $comment = null)
    {
        $xml = new SimpleXMLElement($xml);

        static::assertNotEmpty(
            (array) $xml->xpath($xpath),
            $comment ?: sprintf('Could not find "%s" in "%s"', $xpath, $xml)
        );
    }

    private static function assertXpathEquals($xml, $xpath, $expectedValue, $comment = null)
    {
        static::assertXpathNotEmpty($xml, $xpath);
        $xml = new SimpleXMLElement($xml);
        $elements = $xml->xpath($xpath);
        static::assertEquals($expectedValue, (string)$elements [0]);
    }

    private static function getResultFilePath($runName)
    {
        return __DIR__ . '/../result-' . $runName . '.xml';
    }
}
