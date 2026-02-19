
<?php
$dsn = 'odbc:OracleXE';   
$username = 'rico';
$password = '1234';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "PDO ODBC Connection successful!<br>";

    $stmt = $pdo->query("SELECT * FROM USERS");
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($row);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


// This file is to test if PHP can connect to ORACLE
// To run open this browser
