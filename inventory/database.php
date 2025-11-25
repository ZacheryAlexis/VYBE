<?php
 function getDB() {
   $host = 'sql1.njit.edu';
   $port = 3306;
   $dbname = 'zsa';
   $username = 'zsa';
   $password = 'IT101stuff!';
   mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
     try {
       $db = new mysqli($host, $username, $password, $dbname, $port);
       error_log("You are connected to the $host database!");
       return $db;
     } catch (mysqli_sql_exception $e) {
       // Log the error for admins/developers but do not display details to users
       error_log($e->getMessage());
       // Re-throw so the central exception handler can handle it (and show generic 500 page)
       throw $e;
     }
 }
 //getDB();
?>
