<?php

namespace Ankitfromindia\StarbustQuery;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StarbustQuery
{
    private $connection;
    private $select;
    private static $instance;
    private $query;
    private $limit = 10;
    private $offset = 0;

    private function __construct() {}

    public static function connect($connection = null)
    {
        if (empty(self::$instance)) {
            $instance = new self();
            $instance->setConnection(
                empty($connection) ? config('starbust.default') : $connection

            );
            self::$instance = $instance;
        }
        return self::$instance;
    }
    private function setConnection($connection)
    {

        $host = config('starbust.connections.' . $connection . '.host');
        $port = config('starbust.connections.' . $connection . '.port', 443);
        $user = config('starbust.connections.' . $connection . '.user');
        $password = config('starbust.connections.' . $connection . '.password');
        $catalog = config('starbust.connections.' . $connection . '.catalog');
        $schema = config('starbust.connections.' . $connection . '.schema');
        $driver = config('starbust.connections.' . $connection . '.driver');

        $connectionString = "Driver=$driver;Host=$host;Port=$port;UID=$user;PWD=$password;Catalog=$catalog;Schema=$schema;AuthenticationType=LDAP Authentication;CHARSET=UFT-8";

        try {
            $this->connection =  odbc_connect($connectionString, config('services.aws.key'), config('services.aws.secret'));
        } catch (\SQLException $e) {
            echo "Connection Error";
        }
    }

    public function select($query)
    {
        $this->query = $query;
        return $this;
    }

    private function  row($row, $map)
    {
        if (!is_array($row)) {
            return null; // Or handle the error in another way
        }

        if (is_callable($map)) {
            $row = $map($row);
        }
        return $row;
    }
    public function fetchOne($map = null)
    {
        $this->select = odbc_exec($this->connection, $this->query);
        $row = odbc_fetch_array($this->select);
        //close the connection
        return $this->row($row, $map);
    }

    public function fetch($map = null)
    {
        $data = [];
        $this->select = odbc_exec($this->connection, $this->query);
        while ($row = odbc_fetch_array($this->select)) {
            array_push($data, $this->row($row, $map));
        }
        return $data;
    }

    public function fetchCursor($map = null)
    {
        $this->select = odbc_exec($this->connection, $this->query);

        while ($row = odbc_fetch_array($this->select)) {
            yield $this->row($row, $map);
        }
    }


    public function fetchAndInsertInto($table, $map = null, $chunk = 2000)
    {
        $data = [];
        $counter = 0;

        foreach ($this->fetchCursor($map) as $row) {
            $data[] = $row;
            $counter++;

            if ($counter >= $chunk) {
                $this->insertOrUpdate($data, $table);
                $data = [];
                $counter = 0;
            }
        }

        if (!empty($data)) {
            $this->insertOrUpdate($data, $table);
        }
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        $this->replaceLimitOffset($limit);
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        $this->replaceLimitOffset($this->limit, $offset);
        return $this;
    }


    public function count()
    {

        $countQuery = "SELECT COUNT(*) as total FROM (" . $this->removeLimitOffset() . ") as count_table";
        $select = odbc_exec($this->connection, $countQuery);
        $row = odbc_fetch_array($select);
        return $row['total'];
    }
    public function paginate($limit = 1000, $offset = 0)
    {
        try {
            $this->limit = $limit;
            $this->offset = $offset;
            $this->query = $this->replaceLimitOffset($limit, $offset);

            return [
                'limit' => $limit,
                'offset' => $offset,
                'data' => $this->fetch(),
            ];
        } catch (\Exception $e) {
            return [
                'query' => $this->query,
                'error' => $e->getMessage()
            ];
        }
    }



    private function replaceLimitOffset($limit, $offset = 0)
    {
        // Use regular expressions to find and replace LIMIT and OFFSET clauses
        $query = $this->removeLimitOffset();
        $query .= " OFFSET $offset LIMIT $limit"; // Add new LIMIT and OFFSET
        $this->query = $query;
        return $query;
    }

    private function removeLimitOffset()
    {
        return preg_replace('/\s+OFFSET\s+\d+\s*(?:LIMIT\s+\d+\s*)?$/i', '', $this->query); // Remove existing LIMIT and OFFSET
    }


    public function insertOrUpdate($data, $tableName)
    {
        if (empty($data)) {
            return false;
        }

        $columns = implode(", ", array_keys($data[0]));
        $updateColumns = array_map(function ($column) {
            return "{$column} = VALUES({$column})";
        }, array_keys($data[0]));
        $updateColumns = implode(", ", $updateColumns);

        $values = [];
        foreach ($data as $row) {
            $values[] = "('" . implode("', '", array_map(function ($r) {
                return addslashes($r);
            }, array_values($row))) . "')";
        }
        $values = implode(", ", $values);

        $query = "INSERT INTO {$tableName} ({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updateColumns}";

        // Execute the query here (e.g., using PDO)
        return DB::statement($query);
    }
}
