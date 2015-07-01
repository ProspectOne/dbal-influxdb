<?php
namespace Corley\DBAL\Driver\InfluxDB;

use InfluxDB\Client;
use InfluxDB\Options;
use InfluxDB\Adapter\GuzzleAdapter;
use Doctrine\DBAL\Driver\Connection;
use Corley\DBAL\Driver\InfluxDB\InfluxDBStatement;

class InfluxDBConnection implements Connection
{
    private $client;

    public function __construct(array $params, $username, $password, array $driverOptions = array())
    {
        $http = new \GuzzleHttp\Client();

        $options = new Options();
        $options->setHost($params["host"]);
        $options->setDatabase($params["dbname"]);
        $options->setUsername($username);
        $options->setPassword($password);
        $options->setPort($params["port"]);

        $adapter = new GuzzleAdapter($http, $options);

        $this->client = new Client($adapter);
    }

    public function prepare($prepareString)
    {
        return new InfluxDBStatement($this->client, $prepareString);
    }

    public function query()
    {
        $args = func_get_args();
        $sql = $args[0];
        $stmt = $this->prepare($sql);
        $stmt->execute();

        return $stmt;
    }

    function quote($input, $type=\PDO::PARAM_STR)
    {
        return "'" . addslashes($input) . "'";
    }

    function exec($statement)
    {
        $stmt = $this->query($statement)->execute();
        if (false === $stmt) {
            throw new \RuntimeException("Unable to execute query '{$statement}'");
        }

        return $stmt->rowCount();
    }

    function lastInsertId($name = null)
    {
        throw new \RuntimeException("Unable to get last insert id in InfluxDB");
    }

    function beginTransaction()
    {
        throw new \RuntimeException("Transactions are not allowed in InfluxDB");
    }

    function commit()
    {
        throw new \RuntimeException("Transactions are not allowed in InfluxDB");
    }

    function rollBack()
    {
        throw new \RuntimeException("Transactions are not allowed in InfluxDB");
    }

    function errorCode()
    {

    }

    function errorInfo()
    {

    }
}
