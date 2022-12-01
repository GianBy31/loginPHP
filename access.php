<!DOCTYPE html>
<html>
    <head>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
        
    </body>
</html>

<?php

    session_start();

    $conn = mysqli_connect("localhost", "root", "", "generico");
    $sql = "SELECT name, photoSrc FROM `users` WHERE email = '{$_SESSION['mail']}'";

    $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    $var = "<div class='wrapper'><h1> Benvenuto {$row['name']}</h1></div>";
    $var .= "<center><img src='{$row['photoSrc']}' width='900px'></center>";
    mysqli_close($conn);
    echo $var;
    
?>
