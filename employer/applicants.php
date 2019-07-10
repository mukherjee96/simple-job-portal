<?php
     header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");
    
     require "../connect.php";
     session_start();
     $loggedin = false;
     $error = true;
 
     if(isset($_SESSION["loggedin"])) {
         if($_SESSION["loggedin"] == true) {
             $loggedin = true;
             $userType = $_SESSION["userType"];
             if($userType == 'jobseeker')
                 header("location:../index.php");
             }
         } else {
         header("location:../index.php");
     }

    if(!isset($_REQUEST["job"]))
        header("location:manage-jobs.php");
    
    // $job_id = $_REQUEST["job"];
    $stmt = $con->prepare("SELECT emp_id FROM jobs WHERE id = :id");
    $stmt->execute(array('id' => $_REQUEST["job"]));
    $employer = $stmt->fetch(PDO::FETCH_ASSOC);

    if($_SESSION['id'] != $employer["emp_id"])
        header("location:manage-jobs.php");

    $statement = $con->prepare("SELECT jsid FROM applications WHERE jobid = :job_id");
    if(!$statement->execute(array('job_id' => $_REQUEST["job"]))) {
        $error = true;
    } else {
        $applicants = $statement->fetchAll();    
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
                <li><a href="manage-jobs.php">Manage Jobs</a></li>
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
                <button type="button" class="btn btn-sm btn-round btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
            <div class="content-wrap">
                <div class="mt-2 text-center">
                    <h3>Applicants</h3>
                </div>

                <?php
                    foreach($applicants as $applicant){
                        $sql = "SELECT j.name,j.fresher,j.present_company,j.designation,j.salary,j.experience,u.university,u.yop FROM jobseeker j, jsug u WHERE j.id=:id AND u.jsid=:id";
                        $stmt = $con->prepare($sql);
                        if(!$stmt->execute(array(
                            "id" => $applicant["jsid"]
                        ))) { $error = true; }
                        else {
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo'                        
                            <div class="container">
                                <div class="row justify-content-center">
                                    <div class="col-10">
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5>'.$row["name"].'</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex flex-row flex-wrap mb-2">';
                                                if($row["fresher"] == 'false'){
                                                    echo' <div class="p-2 mr-3"><b>'.$row["designation"].'</b> at <b>'.$row["present_company"].'</b></div>
                                                    <div class="p-2"><b>Experience:</b> '.$row["experience"].'(s)</div>
                                                    <div class="p-2"><b>Current Salary:</b> '.$row["salary"].' LPA</div> ';
                                                } else {
                                                    echo' <div class="p-2">University: '.$row["university"].'</div>
                                                    <div class="p-2"><b>YOP:</b> '.$row["yop"].' year(s)</div>';
                                                }
                                                echo '</div>
                                            </div>
                                            <div class="card-footer text-center">
                                                <a href="#" class="btn btn-outline-dark">View CV</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ';
                        }
                    }
                }
                ?>


            </div>
        </div>

        <!--Footer-->
        <div id="footer" class="footer bg-dark">
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
        <script src="js/employer.js"></script>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>