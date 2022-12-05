<!-- page for user to view their jobs -->
<!doctype html>
<html>
    
    <head>
        
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="headers.css">
        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <style>
            table,
            th,
            td {
                padding: 15px;
                border: 2;
                border-color: rgba(113, 145, 235, 0.822);
                border-style: dashed;
                border-collapse: collapse;
                background-color: azure;
                }
                body {
                    background-color: #FAF9F6;
            }
        </style>
    
    </head>

    <body>


            <!-- Header -->
            <header class=" border-bottom">
                <div class="container-fluid">
                  <a href="about.php">
                        <img src = "company logo.png" class="auto-style1" style="height: 20%; width: 20%;" ></img>
                    </a>
                    <ul class="nav" style="float: right; padding-top: 55px;">
                        <li class="nav-item"><a href="login.html" class="nav-link link-dark px-2">Login</a></li>
                        <li class="nav-item"><a href="signup.html" class="nav-link link-dark px-2">Sign up</a></li>
                    </ul>
                </div>
            </header>

            <nav class="py-2 border-bottom" style="background-color:rgb(252, 223, 185) ;">
                <div class = "container d-flex flex-wrap justify-content-center" style="font-family: Cambria; font-size: larger;">
                    <ul class="nav">
                        <li><a href="about.php"  class="nav-link px-2 link-secondary">ABOUT</a></li>
                        <li><a href="reviews.php" class="nav-link px-2 link-dark">REVIEWS</a></li>
                        <li><a href="request.html" class="nav-link px-2 link-dark">REQUEST</a></li>
                        <li><a href="account.php" class="nav-link px-2 link-dark">ACCOUNT</a></li>
                    </ul>
                </div>
            </nav>
             <!-- Header -->
             <br>

             <?php
        
            session_start();

            $servername = "127.0.0.1";
            $username = "root";
            $password = "mysql";
            $dbname = "chrisppaint";
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $ret = $_SESSION['row'];

            if(empty($ret['account_id'])) {
                header('Location: login.html');
            }

            $id = $ret['account_id'];
            
            //check for admin
            if($ret['admin'] == 0) {
                $sql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job AS job1 WHERE job1.account_id = $id ORDER BY job_id DESC";
            }
            else {
                $sql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job ORDER BY job_id DESC";
            }

            $jobresults = $conn->query($sql);

            ?>

            <div align = "center" class = "container">
                <form method = "post">
                    <input type="text" placeholder = "Search by Job Description or Address" name = "search" size = "100">
                    <select name = "statFilter">
                            <option value = "0">None</option>
                            <option value = "1">Pending</option>
                            <option value = "2">Started</option>
                            <option value = "3">Awaiting Payment</option>
                            <option value = "4">Completed</option>
                        </select>
                    <button class = "btn btn-dark" name = "submit">Search</button>
                </form>

            </div>

            <div align = "center" style = "font-size: 150%;">
            <?php

            if(isset($_POST['submit'])) {

                $search = $_POST['search'];
                $filter = $_POST['statFilter'];

                //check for admin
                if($ret['admin'] == 0) {
                    if($filter == 0)
                    {
                        if(empty($search)){
                            $newsql = $sql;
                        }
                        else {
                            $newsql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job AS job1 WHERE job1.account_id = $id AND (job1.Address LIKE '%$search%' OR job1.Description LIKE '%$search%') ORDER BY job_id DESC";
                        }
                        
                    }
                   else {
                        if(empty($search)){
                            $newsql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job AS job1 WHERE job1.account_id = $id AND job1.status = $filter ORDER BY job_id DESC";
                        }
                        else {
                            $newsql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job AS job1 WHERE job1.account_id = $id AND job1.status = $filter AND (job1.Address LIKE '%$search%' OR job1.Description LIKE '%$search%') ORDER BY job_id DESC";
                        }
                        
                   }
                }
                else {
                    if($filter == 0)
                    {
                        if(empty($search)){
                            $newsql = $sql;
                        }
                        else{
                            $newsql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job WHERE Address LIKE '%$search%' OR Description LIKE '%$search%' ORDER BY job_id DESC";
                        }
                        
                    }
                   else {
                        if(empty($search)) {
                            $newsql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job WHERE status = $filter ORDER BY job_id DESC";
                        }
                        else {
                            $newsql = "SELECT job_id, status, StartDate, Address, Cost, Description FROM job WHERE status = $filter AND (Address LIKE '%$search%' OR Description LIKE '%$search%') ORDER BY job_id DESC";
                        }
                        
                   }
                }

                $jobresults = $conn->query($newsql);

            }

            $i = 0;
            
            echo '<table border = "0">';
            echo '<tr>';
                while($row = $jobresults->fetch_assoc()) {
                    
                    if($i < 4) {
                        echo '<td>';
                            echo '<table border = "1" style = "text-align: center;">';

                                echo '<tr>';
                                    
                                $current_status = $row['status'];

                                switch($current_status) {
                                    case 1:
                                        $current_status = 'Pending';
                                        break;
                                    case 2:
                                        $current_status = 'Started';
                                        break;
                                    
                                    case 3:
                                        $current_status = 'Awaiting Payment';
                                        break;

                                    case 4:
                                        $current_status = 'Completed';
                                        break;
                                }

                                $currentjobid = $row['job_id'];

                                echo '<td style="width: 300px;"><form action = "jobview.php" method = "post"><button type = "submit" name = "address" value = "'. $currentjobid . '" class = "btn-link">' . $row['Address'] . '</button></form></td>';
                                echo '</tr>';
                                echo '<tr>';

                                $sqlphoto = "SELECT photo_id, filename FROM photo AS photo1 WHERE photo1.job_id = $currentjobid ORDER BY photo_id ASC";
                                
                                $photoresults = $conn->query($sqlphoto);
                                
                                $count = 0;

                                while($row2 = $photoresults->fetch_assoc()){
                                    $count++;

                                    echo '<td><img src ="uploads/' . $row2['filename'] . '" class = "gallery" width="300" height="300"></td>';
                                }

                                if($count == 0) {
                                    echo '<td><img src ="No_Image_Available.jpg" class = "gallery" width="300" height="300"></td>';
                                }

                                echo '</tr>';
                                echo '<tr>';
                                echo '<td style="width: 300px">' . $current_status . '</td>';
                                echo '</tr>';
                                echo '<tr>';
                                echo '<td style="width: 300px"> $' . $row['Cost'] . '</td>';
                                echo '</tr>';

                            echo '</table>'; 
                            echo '<br>'; 
                        echo '</td>';

                        $i++;
                    }
                        
                    else {

                                echo '<td>';
                                echo '<table border = "1" style = "text-align: center;">';

                                    echo '<tr>';
                                        
                                    $current_status = $row['status'];

                                    switch($current_status) {
                                        case 1:
                                            $current_status = 'Pending';
                                            break;
                                        case 2:
                                            $current_status = 'Started';
                                            break;
                                        
                                        case 3:
                                            $current_status = 'Awaiting Payment';
                                            break;

                                        case 4:
                                            $current_status = 'Completed';
                                            break;
                                    }

                                    $currentjobid = $row['job_id'];

                                    echo '<td style="width: 300px;"><form action = "jobview.php" method = "post"><button type = "submit" name = "address" value = "'. $currentjobid . '" class = "btn-link">' . $row['Address'] . '</button></form></td>';
                                    echo '</tr>';
                                    echo '<tr>';

                                    $sqlphoto = "SELECT photo_id, filename FROM photo AS photo1 WHERE photo1.job_id = $currentjobid ORDER BY photo_id ASC";
                                    
                                    $photoresults = $conn->query($sqlphoto);
                                    
                                    $count = 0;

                                    while($row2 = $photoresults->fetch_assoc()){
                                        $count++;

                                        echo '<td><img src ="uploads/' . $row2['filename'] . '" class = "gallery" width="300" height="300"></td>';
                                    }

                                    if($count == 0) {
                                        echo '<td><img src ="No_Image_Available.jpg" class = "gallery" width="300" height="300"></td>';
                                    }

                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td style="width: 300px">' . $current_status . '</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td style="width: 300px"> $' . $row['Cost'] . '</td>';
                                    echo '</tr>';

                                echo '</table>'; 
                                echo '<br>'; 
                                echo '</td>';

                            echo '</tr>';

                            $i = 0;
                        }
                }
                echo '</tr>';
            echo '</table>';
                $jobresults->close();
             ?>
        </div>
    </body>

</html>
