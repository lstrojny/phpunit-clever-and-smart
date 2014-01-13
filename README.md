# Clever Test Runner for PHPUnit [![Build Status](https://secure.travis-ci.org/lstrojny/phpunit-clever-and-smart.png)](http://travis-ci.org/lstrojny/phpunit-clever-and-smart)

## Mission
 - Store previous test runs in a database
 - On consecutive test runs, the following order is ensured
   * Failures and Errors
   * Unrecorded tests
   * Slowest tests first

It’s probably not yet very stable but try it out.

### What it does

Run a test suite once with errors

```
PHPUnit 3.7.28 by Sebastian Bergmann.

.............................................FSFS..............  63 / 280 ( 22%)
............................................................... 126 / 280 ( 45%)
............................................................... 189 / 280 ( 67%)
............................................................... 252 / 280 ( 90%)
.........................
```

Rerun that test suite and see how the previous failing tests have been sorted to the beginning of the test run:


```
PHPUnit 3.7.28 by Sebastian Bergmann.

FSFS...........................................................  63 / 280 ( 22%)
............................................................... 126 / 280 ( 45%)
............................................................... 189 / 280 ( 67%)
............................................................... 252 / 280 ( 90%)
.........................
```

## Configuration
To play around with it, add this to your `phpunit.xml(.dist)`
```
    <listeners>
        <listener class="PHPUnit\Runner\CleverAndSmart\TestListener">
            <arguments>
                <object class="PHPUnit\Runner\CleverAndSmart\Storage\Sqlite3Storage"/>
            </arguments>
        </listener>
    </listeners>
```

## Roadmap

 - Test it with as many test suites as possible
 - Let the weight decline over time. The older a test failure is, the less likely it should be it is run first
 - Capture fatal errors through `register_shutdown_function()`
 - Handle `Ctrl+c` through signals
