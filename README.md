# Clever Test Runner for PHPUnit

## Mission
 - Store previous test runs in a database
 - Re-run the tests first that fail more often

It’s probably not yet very stable but try it out.

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
