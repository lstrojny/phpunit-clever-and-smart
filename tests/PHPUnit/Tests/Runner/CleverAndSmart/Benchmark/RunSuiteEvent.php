<?php

namespace PHPUnit\Tests\Runner\CleverAndSmart\Benchmark;

use Athletic\AthleticEvent;
use Symfony\Component\Process\Process;

class RunSuiteEvent extends AthleticEvent
{
    private $sutRoot = 'vendor/symfony/yaml/';
    private $plainConfig = 'plain-phpunit.xml';
    private $mockedConfig = 'mocked-phpunit.xml';
    private $sqliteConfig = 'sqlite-phpunit.xml';
    
    public function classSetUp()
    {
        $org = $this->sutRoot . 'phpunit.xml.dist';
        // plain config to run the suite as-is
        copy($org, $this->plainConfig);
        
        $this->patchPhpunitConfig($this->sqliteConfig, 'PHPUnit\Runner\CleverAndSmart\Storage\Sqlite3Storage');
        $this->patchPhpunitConfig($this->mockedConfig, 'PHPUnit\Runner\CleverAndSmart\Storage\MockedStorage');
    }
    
    private function patchPhpunitConfig($patchedConfigFilename, $storageClass) {
        $org = $this->sutRoot . 'phpunit.xml.dist';
        
        $testlistener = '
        <listeners>
            <listener class="PHPUnit\Runner\CleverAndSmart\TestListener">
                <arguments>
                    <object class="'. $storageClass .'"/>
                </arguments>
            </listener>
        </listeners>';
        
        $content = file_get_contents($org);
        $content = str_replace('<testsuites>', $testlistener. '<testsuites>', $content);
        file_put_contents($patchedConfigFilename, $content);
    }
    
    public function classTearDown()
    {
        unlink($this->plainConfig);
        unlink($this->mockedConfig);
        unlink($this->sqliteConfig);
    }

    /**
     * @baseline
     * @iterations 10
     */
    public function plainSuite()
    {
        $this->runSuite($this->plainConfig);
    }
    
    /**
     * @iterations 10
     */
    public function instrumentedMockedSuite()
    {
        $this->runSuite($this->mockedConfig);
    }
    
    /**
     * @iterations 10
     */
    public function instrumentedSqliteSuite()
    {
        $this->runSuite($this->sqliteConfig);
    }
    
    private function runSuite($config) {
        $cmd = 'phpunit --configuration '. $config .' '. $this->sutRoot;
        
        $process = new Process($cmd);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        
        // no need to output the generated phpunit report on success.
        // we are only interessted how long it took.
    }
}
