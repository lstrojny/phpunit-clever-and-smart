<?php
namespace PHPUnit\Runner\CleverAndSmart\Unit;

use PHPUnit\Runner\CleverAndSmart\PrioritySorter;
use PHPUnit\Runner\CleverAndSmart\Util;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;

class Test extends TestCase
{
}

class PrioritySorterTest extends TestCase
{
    public function testSimpleSorting()
    {
        $suite = new TestSuite('suite1', 'suite1');
        $suite->addTest(new Test('test1'));
        $suite->addTest(new Test('test2'));
        $suite->addTest(new Test('test3'));
        $suite->addTest(new Test('test4'));

        $tests = $suite->tests();
        $this->assertSame('test1', $tests[0]->getName());
        $this->assertSame('test2', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());

        $sorter = new PrioritySorter([['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Test', 'test' => 'test2']]);
        $sorter->sort($suite);
        $tests = $suite->tests();

        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
    }

    public function testSimpleSortingBothMarkedAsErroneous()
    {
        $suite = new TestSuite('suite1', 'suite1');
        $suite->addTest(new Test('test1'));
        $suite->addTest(new Test('test2'));
        $suite->addTest(new Test('test3'));
        $suite->addTest(new Test('test4'));

        $tests = $suite->tests();
        $this->assertSame('test1', $tests[0]->getName());
        $this->assertSame('test2', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());

        $sorter = new PrioritySorter(
            [
                ['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Test', 'test' => 'test2'],
                ['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Test', 'test' => 'test1'],
            ]
        );
        $sorter->sort($suite);
        $tests = $suite->tests();

        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
    }

    public function testSimpleSortingNoErrors()
    {
        $suite = new TestSuite('suite1', 'suite1');
        $suite->addTest(new Test('test1'));
        $suite->addTest(new Test('test2'));
        $suite->addTest(new Test('test3'));
        $suite->addTest(new Test('test4'));

        $tests = $suite->tests();
        $this->assertSame('test1', $tests[0]->getName());
        $this->assertSame('test2', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());

        $sorter = new PrioritySorter([]);
        $sorter->sort($suite);
        $tests = $suite->tests();

        $this->assertSame('test1', $tests[0]->getName());
        $this->assertSame('test2', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
    }

    public function testNestedSorting()
    {
        $suite1 = new TestSuite('suite1', 'suite1');
        $suite2 = new TestSuite('suite2', 'suite2');
        $suite2->addTest(new Test('test3'));
        $suite1->addTestSuite($suite2);
        $suite1->addTest(new Test('test1'));
        $suite1->addTest(new Test('test2'));
        $suite1->addTest(new Test('test3'));
        $suite1->addTest(new Test('test4'));

        $tests = $suite1->tests();
        $this->assertSame('suite2', $tests[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test2', $tests[2]->getName());
        $this->assertSame('test3', $tests[3]->getName());
        $this->assertSame('test4', $tests[4]->getName());

        $sorter = new PrioritySorter([['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Test', 'test' => 'test2']]);
        $sorter->sort($suite1);
        $tests = $suite1->tests();

        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('suite2', $tests[1]->getName());
        $this->assertSame('test1', $tests[2]->getName());
        $this->assertSame('test3', $tests[3]->getName());
        $this->assertSame('test4', $tests[4]->getName());
    }

    public function testNestedSortingNoErrors()
    {
        $suite1 = new TestSuite('suite1', 'suite1');
        $suite2 = new TestSuite('suite2', 'suite2');
        $suite2->addTest(new Test('test3'));
        $suite1->addTestSuite($suite2);
        $suite1->addTest(new Test('test1'));
        $suite1->addTest(new Test('test2'));
        $suite1->addTest(new Test('test3'));
        $suite1->addTest(new Test('test4'));

        $tests = $suite1->tests();
        $this->assertSame('suite2', $tests[0]->getName());
        $this->assertSame('test3', $tests[0]->tests()[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test2', $tests[2]->getName());
        $this->assertSame('test3', $tests[3]->getName());
        $this->assertSame('test4', $tests[4]->getName());

        $sorter = new PrioritySorter([]);
        $sorter->sort($suite1);
        $tests = $suite1->tests();

        $this->assertSame('suite2', $tests[0]->getName());
        $this->assertSame('test3', $tests[0]->tests()[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test2', $tests[2]->getName());
        $this->assertSame('test3', $tests[3]->getName());
        $this->assertSame('test4', $tests[4]->getName());
    }

    public function testSimpleSortingGroups()
    {
        $suite = new TestSuite('suite1', 'suite1');
        $suite->addTest(new Test('test1'), ['g1']);
        $suite->addTest(new Test('test2'), ['g1']);
        $suite->addTest(new Test('test3'), ['g1']);
        $suite->addTest(new Test('test4'), ['g1']);

        $tests = $suite->tests();
        $this->assertSame('test1', $tests[0]->getName());
        $this->assertSame('test2', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
        $tests = Util::getInvisibleProperty($suite, 'groups', 'getGroupDetails')['g1'];
        $this->assertSame('test1', $tests[0]->getName());
        $this->assertSame('test2', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());

        $sorter = new PrioritySorter([['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Test', 'test' => 'test2']]);
        $sorter->sort($suite);
        $tests = $suite->tests();

        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
        $tests = Util::getInvisibleProperty($suite, 'groups', 'getGroupDetails')['g1'];
        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test3', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
    }


    public function testNestedSortingGroups()
    {
        $suite1 = new TestSuite('suite1', 'suite1');
        $suite2 = new TestSuite('suite2', 'suite2');
        $suite2->addTest(new Test('test3'), ['g1']);
        $suite1->addTestSuite($suite2);
        $suite1->addTest(new Test('test1'), ['g1']);
        $suite1->addTest(new Test('test2'), ['g1']);
        $suite1->addTest(new Test('test4'), ['g1']);
        $suite1->addTest(new Test('test5'), ['g1']);

        $tests = $suite1->tests();
        $this->assertSame('suite2', $tests[0]->getName());
        $this->assertSame('test3', $tests[0]->tests()[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test2', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
        $this->assertSame('test5', $tests[4]->getName());
        $tests = Util::getInvisibleProperty($suite1, 'groups', 'getGroupDetails')['g1'];
        $this->assertSame('suite2', $tests[0]->getName());
        $this->assertSame('test3', $tests[0]->tests()[0]->getName());
        $this->assertSame('test1', $tests[1]->getName());
        $this->assertSame('test2', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
        $this->assertSame('test5', $tests[4]->getName());

        $sorter = new PrioritySorter([['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Test', 'test' => 'test2']]);
        $sorter->sort($suite1);

        $tests = $suite1->tests();
        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('suite2', $tests[1]->getName());
        $this->assertSame('test3', $tests[1]->tests()[0]->getName());
        $this->assertSame('test1', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
        $this->assertSame('test5', $tests[4]->getName());
        $tests = Util::getInvisibleProperty($suite1, 'groups', 'getGroupDetails')['g1'];
        $this->assertSame('test2', $tests[0]->getName());
        $this->assertSame('suite2', $tests[1]->getName());
        $this->assertSame('test3', $tests[1]->tests()[0]->getName());
        $this->assertSame('test1', $tests[2]->getName());
        $this->assertSame('test4', $tests[3]->getName());
        $this->assertSame('test5', $tests[4]->getName());
    }
}
