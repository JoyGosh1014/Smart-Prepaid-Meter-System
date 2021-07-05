<?php
include ('server.php') ;

if (!isset($_SESSION['username'])) {
        header('location: login.php');
    }
elseif ((isset($_SESSION['username'])) and ($_SESSION['role'] == "admin")){
        header('location: adindex.php');
    }
    

if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['username']);
        header("location: login.php");
    }


    $username = $_SESSION['username'];
    $query1 = "SELECT mood FROM user_account WHERE username='$username'";
    $result1 = mysqli_query($db, $query1);

    while ($row = mysqli_fetch_array($result1)){
        $mood = $row["mood"];
     }

    echo "Connection: ", $mood;
?>