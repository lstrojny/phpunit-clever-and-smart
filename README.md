# WIP: clever test runner for PHPUnit

## Mission
 - Store previous test runs in a database
 - Re-run the tests first that fail more often

It’s by no means meant for "production".

## TODO
 - Unit tests (how ironic)
 - Typed exception handling

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
