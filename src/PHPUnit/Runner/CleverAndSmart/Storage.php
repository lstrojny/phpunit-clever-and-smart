<?php
namespace PHPUnit\Runner\CleverAndSmart;

use Exception;
use PHPUnit_Framework_AssertionFailedError as AssertionFailedError;
use PHPUnit_Framework_TestCase as TestCase;
use SQLite3;

class Storage
{
    const SCHEMA_VERSION = '0_1_0';

    private $db;

    private function getPrefix()
    {
        return 'phpunit_' . static::SCHEMA_VERSION . '_';
    }

    public function __construct()
    {
        $this->db = new SQLite3('.phpunit-cas.db');
        $this->db->busyTimeout(1000 * 30);
        $this->query('PRAGMA foreign_keys = ON');

        $this->query('BEGIN');
        $this->query(
            'CREATE TABLE IF NOT EXISTS {{prefix}}run (
                run_id CHAR(128) PRIMARY KEY,
                run_ran_at DOUBLE
            )'
        );
        $this->query(
            'CREATE TABLE IF NOT EXISTS {{prefix}}error (
                error_id INTEGER PRIMARY KEY AUTOINCREMENT,
                error_count INTEGER,
                error_class VARCHAR(1024),
                error_test VARCHAR(1024),
                UNIQUE (error_class, error_test) ON CONFLICT REPLACE
            )'
        );
        $this->query(
            'CREATE INDEX IF NOT EXISTS {{prefix}}error_count_idx ON {{prefix}}error(error_count)'
        );
        $this->query(
            'CREATE TABLE IF NOT EXISTS {{prefix}}run_error (
                run_error_id INTEGER PRIMARY KEY AUTOINCREMENT,
                run_id CHAR(128),
                error_id INTEGER,
                UNIQUE (run_id, error_id),
                FOREIGN KEY (error_id) REFERENCES {{prefix}}error(error_id) ON DELETE CASCADE,
                FOREIGN KEY (error_id) REFERENCES {{prefix}}error(error_id)  ON DELETE CASCADE
            )'
        );
        $this->query('COMMIT');
    }

    public function recordError(Run $run, TestCase $test)
    {
        $this->query('BEGIN');
        $this->saveRun($run);
        $this->insertError($run, $test);
        $this->query('COMMIT');
    }

    public function getErrors()
    {
        return $this->select(
            'SELECT error_class AS class, error_test AS test
            FROM {{prefix}}error
            ORDER BY error_count DESC'
        );
    }

    public function recordSuccess(Run $run, TestCase $test)
    {
        $this->query('BEGIN');
        $this->saveRun($run);
        $this->updateErrorCount($test, false);
        $this->query('COMMIT');
    }

    private function insertError(Run $run, TestCase $test)
    {
        $this->query(
            "INSERT OR IGNORE INTO {{prefix}}error (error_class, error_test, error_count)
            VALUES ('%s', '%s', %d)",
            [get_class($test), $test->getName(), 0]
        );
        $errorId = $this->updateErrorCount($test, true);
        $this->insertRelation($run, $errorId);
    }

    private function updateErrorCount(TestCase $test, $increment)
    {
        $this->query(
            "UPDATE {{prefix}}error
            SET error_count = error_count %s 1
            WHERE error_class = '%s' AND error_test = '%s'",
            [($increment ? '+' : '-'), get_class($test), $test->getName()]
        );

        $errorId = $this->selectOne(
            "SELECT error_id FROM {{prefix}}error WHERE error_class = '%s' AND error_test = '%s'",
            [get_class($test), $test->getName()]
        );

        $this->query('DELETE FROM {{prefix}}error WHERE error_count < -5');

        return $errorId;
    }

    private function insertRelation(Run $run, $errorId)
    {
        $this->query(
            "INSERT INTO {{prefix}}run_error (run_id, error_id) VALUES ('%s', %d)",
            [$run->getRunId(), $errorId]
        );
    }

    private function saveRun(Run $run)
    {
        $this->query(
            "INSERT OR IGNORE INTO {{prefix}}run (run_id, run_ran_at) VALUES ('%s', '%s')",
            [$run->getRunId(), $run->getRanAt()->format('U.u')]
        );
    }

    private function query($query, array $params = [])
    {
        $query = $this->prepareQuery($query, $params);

        $this->doQuery($query);
    }

    private function select($query, array $params = [])
    {
        $query = $this->prepareQuery($query, $params);
        $result = $this->doQuery($query);

        $rows = [];
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

        if ($this->db->lastErrorCode() !== 0) {
            throw new Exception($this->db->lastErrorMsg() . ' (error code ' . $this->db->lastErrorCode() . ')');
        }

        return $result;
    }

    private function prepareQuery($query, array $params)
    {
        $query = str_replace('{{prefix}}', $this->getPrefix(), $query);

        return vsprintf($query, array_map([$this->db, 'escapeString'], $params));
    }
}
