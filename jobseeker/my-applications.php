<?php
     header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");
    
     require "../connect.php";
     session_start();
     $loggedin = false;
 
     if(isset($_SESSION["loggedin"])) {
         if($_SESSION["loggedin"] == true) {
             $loggedin = true;
             $userType = $_SESSION["userType"];
             if($userType == 'employer')
                 header("location:../index.php");
             }
         } else {
         header("location:../index.php");
     }

     // Fetch data
     $sql = "SELECT * FROM applications WHERE jsid = '".$_SESSION["id"]."'";
     $statement = $con->prepare($sql);
     $statement->execute();
     $data = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="../css/main.css">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" />
        <link rel="stylesheet" href="../css/main.css">
        <title>Applicants</title>
    </head>
    <body>
        <!--Navbar-->
        <div class="nav-toggle">
            <!--Hamburger Menu-->
            <div class="nav-toggle-bar"></div>
        </div>
        <!--Sidenav-->
        <nav class="nav">
            <ul>
                <li><a href="../">Home</a></li>
                <li><a href="../jobs.php">Jobs</a></li>
                <li><a href="../logout.php">Logout</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li>
                    <div class="d-flex flex-row justify-content-start  mt-3">
                        <div class="p-2 "><a href="#"><i class="fab fa-facebook"></i></a></div>
                        <div class="p-2 "><a href="#"><i class="fab fa-twitter"></a></i></div>
                        <div class="p-2 "><a href="#"><i class="fab fa-linkedin"></a></i></div>
                    </div>
                </li>
            </ul>
        </nav>

        <!--Brand logo-->
        <div class="d-flex align-items-center p-3 bg-grey">
            <h2 class="brand"><a href="../">Job Portal</a></h2>
                
            <div class="btn-group dropleft align-self-end p-2 ml-auto">
                <!--Profile Link-->
                <button type="button" class="btn btn-sm btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                </button>
                <?php
                    if($loggedin == true) {
                        echo '
                            <div class="dropdown-menu">
                                <!--Options-->
                                <a class="dropdown-item disabled" href="#">'.$_SESSION["name"].'</a>
                                <div class="dropdown-divider"></div>              
                                <a class="dropdown-item" href="../logout.php">Logout</a>
                            </div>
                        ';
                    } else {
                        echo '
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#login">Login</a>
                            </div>
                        ';
                    }
                ?>
            </div>
        </div>

        <div class="page-container">
            <div class="content-wrap container-fluid">
            
                <!-- Messages -->
                <div class="alert alert-success" role="alert" id="success" style="display: none;">
                    <p>Your application has been sent.</p>
                </div>

                <div class="alert alert-warning" role="alert" id="warning" style="display: none;">
                    <p>You have already applied to this job.</p>
                </div>

                <!-- Content -->
                <div class="text-center mb-4">
                    <h3>Your Applications</h3>
                </div>

                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Designation</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Status</th>
                                    <!-- <th scope="col">No. of Applicants</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($data as $application) {
                                        $statement = $con->prepare("SELECT e.cname, j.emp_id, j.designation FROM employer e, jobs j WHERE j.id = '".$application["jobid"]."' AND j.emp_id = e.id");
                                        $statement->execute();
                                        $employer = $statement->fetch(PDO::FETCH_ASSOC);
                                        echo '
                                            <tr>
                                                <td>'.$application["date"].'</td>
                                                <td>'.$employer["designation"].'</td>
                                                <td>'.$employer["cname"].'</td>
                                                <td>'.$application["status"].'</td>
                                            </tr>
                                        ';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!--Footer-->
        <div id="footer" class="footer bg-primary-dark">
            <div class="d-flex flex-row justify-content-center bd-highlight mt-3">
                <!--Social Links-->
                <div class="p-2 bd-highlight"><a href="#"><i class="fab fa-facebook"></i></a></div>
                <div class="p-2 bd-highlight"><a href="#"><i class="fab fa-twitter"></a></i></div>
                <div class="p-2 bd-highlight"><a href="#"><i class="fab fa-linkedin"></a></i></div>
            </div>
            <div class="text-center mt-2">
            <a href="#">Privacy Policy</a>
            </div>
        </div>

        <!-- Optional JavaScript -->
        <script src="../js/nav.js"></script>
        <?php
            if(isset($_REQUEST["application"])) {
                if($_REQUEST["application"] == "successful")
                echo '<script src="../js/success.js"></script>';
            }

            if(isset($_REQUEST["application"])) {
                if($_REQUEST["application"] == "present")
                echo '<script src="../js/warning.js"></script>';
            }
        ?>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>