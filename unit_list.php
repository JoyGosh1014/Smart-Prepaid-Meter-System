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

    $yesterday = date("Y-m-d",strtotime("yesterday"));

    $query1 = "SELECT SUM(unit) as total_unit, location FROM unit_history WHERE u_date = '$yesterday' GROUP BY location";
    $result1 = mysqli_query($db, $query1);


    $query2 = "SELECT SUM(unit) as total_unit, location, u_date FROM unit_history WHERE month = $current_month AND year = $current_year AND u_date != '$current_date' GROUP BY location,u_date ";
    $result2 = mysqli_query($db, $query2);

     $query3 = "SELECT SUM(unit) as total_unit, location, month FROM unit_history WHERE year = $current_year AND u_date != '$current_date' GROUP BY location,month ";
    $result3 = mysqli_query($db, $query3);

    $query4 = "SELECT SUM(unit) as total_unit, location, year FROM unit_history WHERE u_date != '$current_date' GROUP BY location,year ";
    $result4 = mysqli_query($db, $query4);



//         $allCorp4 = array( '$match' =>  array('date' => [ '$ne' => $current_date]));

// $theVisible4 = array( '$group' => array( "_id" => array('location' => '$location', 
//     'year' =>'$year'), 'total_unit' => array( '$sum' => '$unit')));
        
//         $result30 = $collection6->aggregate([$allCorp4, $theVisible4]);
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

    <title>Unit Usage</title>
</head>

<body id="unit_list">
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
                        <li class="nav-item"><a href="unit_list.php?logout='1'" class="nav-link">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section id="unit_nav">
        <div class="container">
          <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs nav-fill" id="unitTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="yesterday-tab" data-toggle="tab" href="#yesterday" role="tab" aria-controls="yesterday" aria-selected="true">Last Day</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="month-tab" data-toggle="tab" href="#month" role="tab" aria-controls="month" aria-selected="true">This Month</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="year-tab" data-toggle="tab" href="#year" role="tab" aria-controls="year" aria-selected="true">This Year</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">All</a>
                    </li>
                </ul>
            </div>
            
                            
                    
            <div class="card-body">           
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="yesterday" role="tabpanel" aria-labelledby="yesterday-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="yesterdayTable">
                                <thead class="thead-light ">
                                    <tr>
                                        <th>Area</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                    foreach ($result1 as $data) {
                                        echo '
                                        <tr>
                                      <td class="text-capitalize">'.$data["location"].'</td>
                                      <td>'.$data["total_unit"].'</td>
                                    </tr>'  ;  
                                    }

                                         
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade " id="month" role="tabpanel" aria-labelledby="month-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="monthTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Area</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                  
                                <tbody>
                                    <?php  
                                    foreach ($result2 as $data2) {
                                       
                                        
                                        echo '
                                        <tr>
                                      
                                      <td>'.$data2["u_date"].'</td>
                                      <td class="text-capitalize">'.$data2["location"].'</td>
                                      <td>'.$data2["total_unit"].'</td>
                                      
                                    </tr>'  ;  
                                    }

                                         
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="year" role="tabpanel" aria-labelledby="year-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="yearTable">
                                <thead class="thead-light ">
                                    <tr>
                                        <th>Month</th>
                                        <th>Area</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                    foreach ($result3 as $data3) {
                                        $month = $data3["month"];
                                        $text_month = "";
                                        if($month == '01'){
                                            $text_month = 'January';
                                        }
                                        if($month == '02'){
                                            $text_month = 'February';
                                        }
                                        if($month == '03'){
                                            $text_month = 'March';
                                        }
                                        if($month == '04'){
                                            $text_month = 'April';
                                        }
                                        if($month == '05'){
                                            $text_month = 'May';
                                        }
                                        if($month == '06'){
                                            $text_month = 'June';
                                        }
                                        if($month == '07'){
                                            $text_month = 'July';
                                        }
                                        if($month == '08'){
                                            $text_month = 'August';
                                        }
                                        if($month == '09'){
                                            $text_month = 'September';
                                        }
                                        if($month == '10'){
                                            $text_month = 'October';
                                        }
                                        if($month == '11'){
                                            $text_month = 'November';
                                        }
                                        if($month == '12'){
                                            $text_month = 'December';
                                        }
                                        echo '
                                        <tr>
                                      <td>'.$text_month.'</td>
                                      <td class="text-capitalize">'.$data3["location"].'</td>
                                      <td>'.$data3["total_unit"].'</td>
                                    </tr>'  ;  
                                    }

                                         
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                        <div class="p-3">
                            <table class="table text-center table-bordered table-striped table-primary table-responsive-sm" id="allTable">
                                <thead class="thead-light ">
                                    <tr>
                                        <th>Year</th>
                                        <th>Area</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php  
                                    foreach ($result4 as $data4) {
                                        echo '
                                        <tr>
                                      <td>'.$data4["year"].'</td>
                                      <td class="text-capitalize">'.$data4["location"].'</td>
                                      <td>'.$data4["total_unit"].'</td>
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
            $('#yesterdayTable').DataTable();
            $('#monthTable').DataTable();
            $('#yearTable').DataTable();
            $('#allTable').DataTable();
        } );
    </script>

    <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
</body>

</html>