<?php 
include ('server.php') ;
if (!isset($_SESSION['username'])) {
        header('location: login.php');
    }
elseif ((isset($_SESSION['username'])) and ($_SESSION['role'] == "user")){
        header('location: index.php');
    }
    

if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['username']);
        header("location: login.php");
    }


    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query1 = "SELECT * FROM users WHERE id = '$id'";
        $result1 = mysqli_query($db, $query1);
        while ($row = mysqli_fetch_array($result1)) {
            $username = $row['username'];
            $name = $row['name'];
            $mobile_number = $row['mobile_number'];
            $national_id = $row['national_id'];
            $email = $row['email'];
        }

    }
    // else{
    //     header('location: adindex.php');
    // }

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/title_logo1.jpg">
    <title>Update User</title>
</head>
<body id="add_user">
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
                      <li class="nav-item"><a href="update_user.php?logout='1'" class="nav-link" >Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
            <div class="row justify-content-center">
                <div class = "col-sm-5">
                    <!-- notification message -->
                    
                    <div class="card ">
                       <div class="card-header bg-primary text-center font-weight-bolder text-white">
                        <h1>Update User</h1>
                        </div>
                        <div class="card-body">
                            <form method="post" action="update_user.php">
                                <?php include('errors.php'); ?>


                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" readonly >
                                </div>

                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="<?php echo $name; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="mobile_number">Mobile Number:</label>
                                    <input type="text" class="form-control" id="mobile_number" placeholder="Enter Mobile Number" name="mobile_number" value="<?php echo $mobile_number; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="NID">NID No:</label>
                                    <input type="text" class="form-control" id="national_id" 
                                    placeholder="Enter National ID Card Number" name="national_id" value="<?php echo $national_id; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" 
                                    placeholder="Enter Email Address" name="email" value="<?php echo $email; ?>">
                                </div>
                                <input type="button" class="btn btn-light" value="Go Back" onClick="history.go(-1);">
                                <button type="submit" name = "update" class="btn btn-primary">Update</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
    <!-- <script>
    function goBack() {
      window.history.back();
    }
    </script> -->
  </body>
</html>