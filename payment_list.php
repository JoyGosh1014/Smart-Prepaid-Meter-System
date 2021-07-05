<?php
include ('server.php') ;
//session_start();

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

if (isset($_GET['id']) and $_GET['req'] and $_GET['amount'] and $_GET['username']) {
    $id = $_GET['id'];
    $req = $_GET['req'];
    $amount = $_GET['amount'];
    $username = $_GET['username'];
    if($req == 'acpt'){
        $amount = (double)$amount;
        $demand_charge = 15;
        $meter_rent = 40;
        $service_charge = 10;
        $percentage = ($amount * 5)/100;
        $query1 = "SELECT * FROM payment WHERE username ='$username' AND year = '$current_year' AND month = '$current_month' AND status = 'accepted'";
        $result1 = mysqli_query($db, $query1);

        if(mysqli_num_rows($result1) >= 1){
                $amount = $amount - $percentage;
        }
        else{
                $amount = $amount - ($percentage + $demand_charge + $service_charge + $meter_rent);
        }

        $query2 = "SELECT * FROM user_account WHERE username ='$username'";
        $result2 = mysqli_query($db, $query2);
        while($row = mysqli_fetch_assoc($result2)) {
                $balance = (double)$row['balance'];
                $loan_status = $row['loan'];
        } 
            
        if($loan_status == '0'){
                $amount = $amount - 50;
                $query3 = " UPDATE user_account SET loan = '1' WHERE username = '$username'";
                mysqli_query($db, $query3);
        }

        $balance = $balance + $amount;
        $query4 = " UPDATE user_account SET balance = '$balance' WHERE username = '$username'";
        mysqli_query($db, $query4);

        $query5 = " UPDATE payment SET status = 'accepted' WHERE id = '$id'";
        mysqli_query($db, $query5);

        $query6 = "SELECT email FROM users WHERE username ='$username'";
        $result6 = mysqli_query($db, $query6);

        while($row = mysqli_fetch_assoc($result6)) {
                $email = $row['email'];
        } 
        ini_set('display_errors',1);
        error_reporting( E_ALL );
        $from = "abirdas@abirdas.xyz";
        $to = $email;
        $subject = "Premaid Meter Payment Message";
        $message = "Dear User, your payment request is accepted and ".$amount."tk is added to your account";
        $headers = "From:" . $from;

       mail($to, $subject, $message, $headers);
        header('location: payment_list.php');
    
    }

    if($req == 'dlt'){

    $query7 = "UPDATE payment SET status = 'deleted' WHERE id = '$id'";
    if(mysqli_query($db, $query7)){
        $query8 = "SELECT email FROM users WHERE username ='$username'";
        $result8 = mysqli_query($db, $query8);
        while($row = mysqli_fetch_assoc($result8)) {
            $email = $row['email'];
        } 
        ini_set('display_errors',1);
        error_reporting( E_ALL );
        $from = "abirdas@abirdas.xyz";
        $to = $email;  
        $to_email = $email;
        $subject = "Premaid Meter Payment Message";
        $message = "Dear User your payment request is delete please contact with admin";
        $headers = "From:" . $from;

        mail($to, $subject, $message, $headers);
        header('location: payment_list.php');
    }
    }
    
}


$query9 = "SELECT * FROM payment WHERE status ='accepted'";
$result9 = mysqli_query($db, $query9);

$query10 = "SELECT * FROM payment WHERE status ='pending'";
$result10 = mysqli_query($db, $query10);

$query11 = "SELECT * FROM payment WHERE status ='deleted'";
$result11 = mysqli_query($db, $query11);


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

    <title>Payment List</title>
</head>

<body id="payment_list">
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
                        <li class="nav-item"><a href="payment_list.php?logout='1'" class="nav-link">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section id="payment_nav">
        <div class="container">
          <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs nav-fill" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="admin_approved-tab" data-toggle="tab" href="#admin_approved" role="tab" aria-controls="admin_approved" aria-selected="true">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="admin_pending-tab" data-toggle="tab" href="#admin_pending" role="tab" aria-controls="admin_pending" aria-selected="true">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="admin_deleted-tab" data-toggle="tab" href="#admin_deleted" role="tab" aria-controls="admin_deleted" aria-selected="true">Deleted</a>
                    </li>
                </ul>
            </div>
            
                            
                    
            <div class="card-body">           
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="admin_approved" role="tabpanel" aria-labelledby="admin_approved-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="admin_approvedTable">
                                <thead class="thead-light ">
                                    <tr>
                                        <th>Username</th>
                                        <th>Mobile Number</th>
                                        <th>Payment Type</th>
                                        <th>Money Recipt No/ Trx ID</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                    foreach ($result9 as $data) {
                                        echo '
                                        <tr>
                                      <td>'.$data["username"].'</td>
                                      <td class=text-uppercase>'.$data["mobile_number"].'</td>
                                      <td>'.$data["type"].'</td>
                                      <td class=text-lowercase>'.$data["recipt"].'</td>
                                      <td>'.$data["amount"].'</td>
                                      <td>'.$data["p_date"].'</td>
                                      
                                    </tr>'  ;  
                                    }

                                         
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade " id="admin_pending" role="tabpanel" aria-labelledby="admin_pending-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="admin_pendingTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Bkash Account No</th>
                                        <th>Trx ID</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                  
                                <tbody>
                                    <?php  
                                    foreach ($result10 as $data) {
                                        echo '
                                        <tr>
                                      <td>'.$data["username"].'</td>
                                      <td class=text-uppercase>'.$data["mobile_number"].'</td>
                                      <td class=text-lowercase>'.$data["recipt"].'</td>
                                      <td>'.$data["amount"].'</td>
                                      <td>'.$data["p_date"].'</td>
                                      <td>
                                      <button type="button" class="btn btn-sm btn-success"><a class="text-white" href="payment_list.php?id='.$data["id"].'&req=acpt&amount='.$data["amount"].'&username='.$data["username"].'">Accept</a></button> <button type="button" class="btn btn-sm btn-danger"><a class="text-white" href="payment_list.php?id='.$data["id"].'&req=dlt&amount='.$data["amount"].'&username='.$data["username"].'">Delete</a></button>
                                      </td>
                                    </tr>'  ;  
                                    }

                                         
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="admin_deleted" role="tabpanel" aria-labelledby="admin_deleted-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="admin_deletedTable">
                                <thead class="thead-light ">
                                    <tr>
                                        <th>Username</th>
                                        <th>Bkash Account No</th>
                                        <th>Trx ID</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php  
                                    foreach ($result11 as $data) {
                                        echo '
                                        <tr>
                                      <td>'.$data["username"].'</td>
                                      <td class=text-uppercase>'.$data["mobile_number"].'</td>
                                      <td class=text-lowercase>'.$data["recipt"].'</td>
                                      <td>'.$data["amount"].'</td>
                                      <td>'.$data["p_date"].'</td>
                                      
                                    </tr>'  ;  
                                    }

                                         
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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
            $('#admin_approvedTable').DataTable();
            $('#admin_pendingTable').DataTable();
            $('#admin_deletedTable').DataTable();
            
        } );
    </script>

    <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
</body>

</html>