# Clever Test Runner for PHPUnit
[![Build Status](https://secure.travis-ci.org/lstrojny/phpunit-clever-and-smart.svg)](http://travis-ci.org/lstrojny/phpunit-clever-and-smart) [![Dependency Status](https://www.versioneye.com/user/projects/542d5df4fc3f5cd7000001fb/badge.svg?style=flat)](https://www.versioneye.com/user/projects/542d5df4fc3f5cd7000001fb) [![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/lstrojny/phpunit-clever-and-smart.svg)](http://isitmaintained.com/project/lstrojny/phpunit-clever-and-smart "Average time to resolve an issue") [![Percentage of issues still open](http://isitmaintained.com/badge/open/lstrojny/phpunit-clever-and-smart.svg)](http://isitmaintained.com/project/lstrojny/phpunit-clever-and-smart "Percentage of issues still open")

## Mission
Enable fast feedback cycles by storing test case results in a database and reorder tests on consecutive runs in the
following order:
  1. Failures and errors
  2. So far unrecorded tests
  3. Remaining tests by execution time in ascendant order (fastest first)

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

## Installation

add the following line to your projects' composer.json `require-dev` section.

```json
"lstrojny/phpunit-clever-and-smart": "0.*"
```

## Configuration
To play around with it, add this to your `phpunit.xml(.dist)`

```xml
    <listeners>
        <listener class="PHPUnit\Runner\CleverAndSmart\TestListener">
            <arguments>
                <object class="PHPUnit\Runner\CleverAndSmart\Storage\Sqlite3Storage"/>
            </arguments>
        </listener>
    </listeners>
```

you might alter the location of the sqlite storage file, by passing a path to the Sqlite3Storage class:

```xml
    <listeners>
        <listener class="PHPUnit\Runner\CleverAndSmart\TestListener">
            <arguments>
                <object class="PHPUnit\Runner\CleverAndSmart\Storage\Sqlite3Storage">
                    <arguments>
                        <string>/my/path/to/.phpunit-cas.db</string>
                    </arguments>
                </object>
            </arguments>
        </listener>
    </listeners>
```

## Roadmap

 - Test it with as many test suites as possible
 - Stabilize
 - Merge into PHPUnit core
