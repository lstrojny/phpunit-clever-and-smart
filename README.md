# WIP: clever test runner for PHPUnit

## Mission
 - Store previous test runs in a database
 - Re-run the tests first that fail more often

It’s by no means meant for "production".

## TODO
 - Deal with `@depends`
 - Deal with data providers
 - Unit tests (how ironic)

## Configuration
To play around with it, add this to your `phpunit.xml(.dist)`
```
    <listeners>
        <listener
            class="PHPUnit\Runner\CleverAndSmart\TestListener"
            file="src/PHPUnit/Runner/CleverAndSmart/TestListener.php">
            <arguments>
            </arguments>
        </listener>
    </listeners>
```
