<?php 
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'online_grocery_db';

    // Set DSN
    $dsn = 'mysql:host='. $host . ';dbname='. $dbname;

    //Create a PDO instance
    $pdo = new PDO($dsn, $user, $password);

    # PRDO QUERY 

    $stmt = $pdo->query('SELECT * FROM users');

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        echo $row['username'].'__' .$row['email']. '<br>';
    }
?>