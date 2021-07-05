<?php
include ('server.php') ;
//session_start();

if (!isset($_SESSION['username'])) {
    header('location: login.php');
} elseif ((isset($_SESSION['username'])) and ($_SESSION['role'] == "user")) {
    header('location: index.php');
}


if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
}
    //Show Users Data
    $query1 = "SELECT * FROM users WHERE role ='user'";
    $result1 = mysqli_query($db, $query1);
    
    //Control Mood
    if (isset($_GET['id']) and isset($_GET['mood'])) {
        $id = $_GET['id'];
        $query2 = "SELECT * FROM user_account WHERE id = '$id'";
        $result2 = mysqli_query($db, $query2);
    
    while ($row = mysqli_fetch_array($result2)) {
        if($row['mood'] == 'under construction'){
            $query3 = "UPDATE user_account SET mood = 'stable' WHERE id = '$id'";

            mysqli_query($db,$query3);

        }
        else{
            $query3 = "UPDATE user_account SET mood = 'under construction' WHERE id = '$id'";

            mysqli_query($db,$query3);
        }

       header('location: user_list.php');
    }
}

    //Delete User
    if(isset($_GET['username'])){
        $username = $_GET['username'];
        $query5 = "DELETE FROM users WHERE username = '$username'";
        if(mysqli_query($db,$query5)){
            $query6 = "DELETE FROM user_account WHERE username = '$username'";
            mysqli_query($db,$query6);
            $query7 = "DELETE FROM total_unit WHERE username = '$username'";
            mysqli_query($db,$query7);
            $query7 = "UPDATE payment SET show_data = '0' WHERE username = '$username'";
            mysqli_query($db,$query7);
        header('location: user_list.php');
    }
    }

    if(isset($_GET['user'])){
        $username = $_GET['user'];
        $password = md5('12345');

         $query8 = "UPDATE users SET password = '$password' WHERE username = '$username'";

        mysqli_query($db,$query8);

        header('location: user_list.php');
    }

?>
<!doctype html>

<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <link rel="stylesheet" href="//cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/title_logo1.jpg">

    <title>User List</title>
</head>

<body id="user_list">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container-fluid">
                <h4>
                    <a href="adindex.php" class="navbar-brand">Prepaid Meter</a>
                </h4>
                <button type="button" class="navbar-toggler btn-sm" data-toggle="collapse" data-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="menu" class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a href="user_list.php?logout='1'" class="nav-link">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section id="user_nav">
        <div class="container">
          
            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="userTable">
                <thead class="thead-light ">
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Mobile Number</th>
                        <th>Connection Mood</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  
                        foreach ($result1 as $data) {
                            $username = $data["username"];
                            $query4 = "SELECT * FROM user_account WHERE username = '$username' ";
                            $result4 = mysqli_query($db, $query4);
                            while ($row = mysqli_fetch_array($result4)) {
                                // $mood = $row["mood"];
                                // $id = $row["id"];
                            

                            if($row["mood"] == 'under construction'){
                                echo '
                            <tr>
                            <td>'.$data["username"].'</td>
                            <td class = "text-capitalize" >'.$data["name"].'</td>
                            <td>'.$data["mobile_number"].'</td>
                            <td><button type="button" class="btn btn-danger"><a href="user_list.php?id='.$row["id"].'&mood='.$row["mood"].'" class = "text-white text-capitalize">'.$row["mood"].'</a></button></td>
                            <td><button type="button" class="btn btn-success mb-2"><a href="update_user.php?id='.$data["id"].'" class = "text-white">Update</a></button>
                            <button type="button" class="btn btn-danger mb-2"><a href="user_list.php?username='.$data["username"].'" class = "text-white">Delete</a></button>
                            <button type="button" class="btn btn-primary mb-2"><a href="user_list.php?user='.$data["username"].'" class = "text-white">Set Password</a></button>
                            </td>
                            </tr>'  ;
                            }
                            else{
                                 echo '
                            <tr>
                            <td>'.$data["username"].'</td>
                            <td class = "text-capitalize" >'.$data["name"].'</td>
                            <td>'.$data["mobile_number"].'</td>
                            <td><button type="button" class="btn btn-success"><a href="user_list.php?id='.$row["id"].'&mood='.$row["mood"].'" class = "text-white text-capitalize">'.$row["mood"].'</a></button></td>
                            <td><button type="button" class="btn btn-success mb-2"><a href="update_user.php?id='.$data["id"].'" class = "text-white">Update</a></button>
                            <button type="button" class="btn btn-danger mb-2"><a href="user_list.php?username='.$data["username"].'" class = "text-white">Delete</a></button>
                            <button type="button" class="btn btn-primary mb-2"><a href="user_list.php?user='.$data["username"].'" class = "text-white">Set Password</a></button>
                            </td>
                            </tr>'  ;
                            }

                        }
                              
                            

                            }

                    ?>          
                </tbody>
            </table>
        </div>
    </section>

    

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <script src="//cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready( function () {
            $('#userTable').DataTable();
        } );
    </script>

    
</body>

</html>