<?php
include ('server.php') ;
//session_start();

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


if (isset($_GET['username']) and $_GET['port']) {
        $username = $_GET['username'];
        $port = $_GET['port'];

          $query1 = "SELECT * FROM user_account WHERE username='$username'";
        $result1 = mysqli_query($db, $query1);
        while ($row = mysqli_fetch_array($result1)) { 
            if($port == 'switch1'){
                if($row['switch_1'] == 0){
                    $query2 = "UPDATE user_account SET switch_1 = 1 WHERE username='$username'";

                    mysqli_query($db,$query2);
                }
                else{
                    $query2 = "UPDATE user_account SET switch_1 = 0 WHERE username='$username'";

                    mysqli_query($db,$query2);
                }
            }

            if($port == 'switch2'){
                if($row['switch_2'] == 0){
                    $query3 = "UPDATE user_account SET switch_2 = 1 WHERE username='$username'";

                    mysqli_query($db,$query3);
                }
                else{
                    $query3= "UPDATE user_account SET switch_2 = 0 WHERE username='$username'";

                    mysqli_query($db,$query3);
                }
            }
        }

        header('location: index.php');
    }

    echo '
    <button type="button" class="btn btn-light"><a href="control.php?username='.$username.'&port=switch1">Switch 1</a></button>

    <button type="button" class="btn btn-light"><a href="control.php?username='.$username.'&port=switch2">Switch 2</a></button>
    ';

?>