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
    $query1 = "SELECT balance FROM user_account WHERE username='$username'";
    $result1 = mysqli_query($db, $query1);
    $query2 = "SELECT unit_minute FROM total_unit WHERE username='$username'";
    $result2 = mysqli_query($db, $query2);

    $query3 = "SELECT mood FROM user_account WHERE username='$username'";
    $result3 = mysqli_query($db, $query3);

    while ($row = mysqli_fetch_array($result3)){
        $mood = $row["mood"];
     }

    echo "<p class=font-weight-bolder>Your connection is ", $mood ."</p>";

     while ($row = mysqli_fetch_array($result1)){
        $total_balance = $row["balance"];
     }

     while ($row = mysqli_fetch_array($result2)){
         $total_unit = $row["unit_minute"];
     }

    $balance = ((double)$total_balance - ((double)$total_unit * 0.0001)); 

    if($balance > 0){
        echo "<p class=font-weight-bolder>and balance is ", $balance, "tk</p>";
    }else{
        echo "<p class=font-weight-bolder>and balance is 0tk</p>";
    }

    
?>