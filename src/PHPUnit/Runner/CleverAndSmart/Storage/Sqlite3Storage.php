<?php
namespace PHPUnit\Runner\CleverAndSmart\Storage;

use Closure;
use PHPUnit\Runner\CleverAndSmart\Exception\StorageException;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit\Runner\CleverAndSmart\Run;
use Exception;
use SQLite3;

class Sqlite3Storage implements StorageInterface
{
    const SCHEMA_VERSION = '0_4_0';

    private $db;

    private function getPrefix()
    {
        return 'phpunit_' . static::SCHEMA_VERSION . '_';
    }

    public function __construct($fileName = '.phpunit-cas.db')
    {
        $this->db = new SQLite3($fileName);

        // method introduced in php 5.3.3
        if (method_exists($this->db, 'busyTimeout')) {
            $this->db->busyTimeout(1000 * 30);
        }

        // PRAGMA need to be set outside a transaction
        $this->query('PRAGMA foreign_keys = ON');
        $this->query('PRAGMA page_size = 4096');
        $this->query('PRAGMA cache_size = 10000');
        $this->query('PRAGMA synchronous = OFF');
        $this->query('PRAGMA journal_mode = MEMORY');

        $this->transactional(array($this, 'initDatabaseSchema'));
    }

    public function initDatabaseSchema()
    {
        $this->query(
            'CREATE TABLE IF NOT EXISTS {{prefix}}run (
                run_id INTEGER PRIMARY KEY AUTOINCREMENT,
                run_identifier CHAR(128),
                run_ran_at DOUBLE
            )'
        );
        $this->query(
            'CREATE UNIQUE INDEX IF NOT EXISTS
            u_{{prefix}}run_identifier_idx ON {{prefix}}run(run_identifier)'
        );
        $this->query(
            'CREATE TABLE IF NOT EXISTS {{prefix}}result (
                result_id INTEGER PRIMARY KEY AUTOINCREMENT,
                run_id INTEGER,
                result_class VARCHAR(1024),
                result_test VARCHAR(1024),
                result_identifier CHAR(128),
                result_state TINYINT,
                result_time DOUBLE,
                FOREIGN KEY (run_id) REFERENCES {{prefix}}run(run_id) ON DELETE CASCADE
            )'
        );
        $this->query(
            'CREATE INDEX IF NOT EXISTS
            {{prefix}}result_idx ON {{prefix}}result(result_state, result_identifier)'
        );
    }

    public function record(Run $run, TestCase $test, $time, $status)
    {
        $this->transactional(array($this, 'doRecord'), $run, $test, $time, $status);
    }

    public function doRecord(Run $run, TestCase $test, $time, $status)
    {
        $this->insertResult($this->storeRun($run), $test, $time, $status);
    }

    public function getRecordings(array $types, $includeTime = true)
    {
        $query = 'FROM {{prefix}}result
                WHERE result_state IN (%s)
                GROUP BY result_identifier
                ORDER BY COUNT(*) DESC';

        if ($includeTime) {
            $query = 'SELECT result_class AS class, result_test AS test, AVG(result_time) AS time ' . $query;
        } else {
            $query = 'SELECT result_class AS class, result_test AS test ' . $query;
        }

        return $this->select($query, array($types));
    }

    private function transactional($callable /*, ... $args */)
    {
        $this->query('BEGIN');

        $args = func_get_args();
        array_shift($args);

        try {
            $result = call_user_func_array($callable, $args);
        } catch (Exception $e) {
            $this->query('ROLLBACK');
            throw $e;
        }

        $this->query('COMMIT');

        return $result;
    }

    private function insertResult($runId, TestCase $test, $time, $status)
    {
        $className = get_class($test);
        $testName = $test->getName();
        $identifier = hash('sha512', $className . $testName);

        $this->query(
            "INSERT INTO {{prefix}}result
                (run_id, result_class, result_test, result_identifier, result_state, result_time)
            VALUES
                (%d, '%s', '%s', '%s', %d, %F)",
            array($runId, $className, $testName, $identifier, $status, $time)
        );

        if ($status < static::STATUS_FAILURE) {
            $this->query(
                "DELETE FROM {{prefix}}result
                WHERE result_identifier = '%s'
                AND (SELECT COUNT(*) FROM {{prefix}}result WHERE result_identifier = '%s' AND result_state < %d) >= 4",
                array($identifier, $identifier, StorageInterface::STATUS_FAILURE)
            );
        }
    }

    private function storeRun(Run $run)
    {
        $this->query(
            "INSERT OR IGNORE INTO {{prefix}}run (run_identifier, run_ran_at) VALUES ('%s', '%s')",
            array($run->getRunIdentifier(), $run->getRanAt())
        );

        return $this->selectOne(
            "SELECT run_id FROM {{prefix}}run WHERE run_identifier = '%s'",
            array($run->getRunIdentifier())
        );
    }

    public function query($query, array $params = array())
    {
        $query = $this->prepareQuery($query, $params);

        return $this->doQuery($query);
    }

    private function select($query, array $params = array())
    {
        $result = $this->query($query, $params);

        $rows = array();
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }

        return $rows;
    }

    private function selectOne($query, array $params)
    {
        $rows = $this->select($query, $params);

        if ($rows) {
            return current(current($rows));
        }
    }

    private function doQuery($query)
    {
        $result = $this->db->query($query);

        if ($this->db->lastErrorCode() > 0) {
            throw StorageException::databaseError($this->db->lastErrorMsg(), $this->db->lastErrorCode());
        }

        return $result;
    }

    private function prepareQuery($query, array $params)
    {
        $query = str_replace('{{prefix}}', $this->getPrefix(), $query);

        $escapedParams = array();
        foreach ($params as $param) {
            if (is_string($param) || is_object($param)) {
                $escapedParams[] = $this->db->escapeString($param);
            } elseif (is_array($param)) {
                $escapedParams[] = "'" . join("', '", array_map(array($this->db, 'escapeString'), $param)) . "'";
            } else {
                $escapedParams[] = $param;
            }
        }

        return vsprintf($query, $escapedParams);
    }
}
