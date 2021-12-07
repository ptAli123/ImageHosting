<?php

namespace App\Services;
use MongoDB\Client as mongo;
use Illuminate\Http\Request;
class DatabaseConnectionService
{
    public function getConnection($table)
    {
        $collection=(new mongo)->imageHosting->$table;
        return $collection;
    }
}
