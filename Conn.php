<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 'On');
require_once(__DIR__ . '/wp-config.php');
class DbInterface
{
    var $conn;

    function __construct()
    {

        $servername = DB_HOST;
        $username = DB_USER;
        $password = DB_PASSWORD;
        $database = DB_NAME;

        $this->conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    function GetConnectionObj()
    {
        return $this->conn;
    }

    //echo "Connected successfully";
    function getTable($sql)
    {
        $result = $this->conn->query($sql);
        return $result;
    }
    
    //<!--start update 19-feb-2019-->

    function ExecuteMultipleQuery($sql)
    {

        $result = mysqli_multi_query($this->conn, $sql);
        return $result;
    }
    //<!--end update 19-feb-2019-->

    function getTableList($sql)
    {


    }

}



