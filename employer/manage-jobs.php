<?php
    header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");
    
    require "../connect.php";
    session_start();
    $loggedin = false;

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

    if(isset($_POST['addJobBtn'])) {
        $error = false;

        $id = $_SESSION["id"];
        $title = filter_var($_POST["emp-title"],FILTER_SANITIZE_STRING);
        $designation = filter_var($_POST["emp-designation"],FILTER_SANITIZE_STRING);
        $description = filter_var($_POST["emp-description"],FILTER_SANITIZE_STRING);
        $requirement = filter_var($_POST["emp-requirement"],FILTER_SANITIZE_STRING);
        $salary = filter_var($_POST["emp-salary"],FILTER_SANITIZE_NUMBER_FLOAT);
        $experience = filter_var($_POST["emp-experience"],FILTER_SANITIZE_NUMBER_FLOAT);
        $location = filter_var($_POST["emp-location"],FILTER_SANITIZE_STRING);
        
        $sql = "INSERT INTO jobs(emp_id,title,designation,description,requirements,salary,experience,location,highlighted,available) VALUES('$id','$title','$designation','$description','$requirement','$salary','$experience','$location','false','true')";

        $statement = $con->prepare($sql);
        $statement->execute();
        if(!$statement->rowCount())
            $error = true;
        
        if(!$error) {
            header("Location: manage-jobs.php?success=true");
        } else {
            header("Location: manage-jobs.php?failed=true");
        }
    }
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
        <title>Job Portal | Home</title>
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
                <li><a href="../logout.php">Logout</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li>
                    <div class="d-flex flex-row justify-content-start bd-highlight mt-3">
                        <div class="p-2 bd-highlight"><a href="#"><i class="fab fa-facebook"></i></a></div>
                        <div class="p-2 bd-highlight"><a href="#"><i class="fab fa-twitter"></a></i></div>
                        <div class="p-2 bd-highlight"><a href="#"><i class="fab fa-linkedin"></a></i></div>
                    </div>
                </li>
            </ul>
        </nav>

        <!--Brand logo-->
        <div class="d-flex align-items-center p-3 bg-grey">
            <h2 class="brand"><a href="#">Job Portal</a></h2>
            
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
            <div class="content-wrap container-fluid">
            
                <!-- Messages -->
                <div class="alert alert-success" role="alert" id="success" style="display:none;">
                    <p>A new job was added.</p>
                </div>

                <div class="alert alert-danger" role="alert" id="jobNotAdded" style="display:none;">
                    <p>Job was not added. Please try again.</p>
                </div>

                <!-- Post Job Button -->
                <div class="container">
                    <div class="row justify-content-center mb-5">
                        <div class="col-sm-4">
                            <button class="btn btn-block btn-secondary"  data-toggle="modal" data-target="#add-a-job">Post a Job</button>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="justify-content-center text-center m-5">
                    <h3>Jobs posted by you</h3>
                </div>
                <!-- Cards -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-sm-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex bd-highlight">
                                        <div class="mr-auto p-2 bd-highlight"><h5 class="card-title">Web Developer Required</h5></div>
                                        <div class="p-2 bd-highlight"><a href="#" class="card-link"><i class="fas fa-edit text-dark"></i></a></div>
                                        <div class="p-2 bd-highlight"><a href="#" class="card-link"><i class="far fa-trash-alt text-dark"></i></a></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <p class="card-text m-1"><strong>Designation:</strong> Developer  </p>
                                        <p class="card-text m-1"><strong>CTC:</strong> 4 LPA</p>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <p class="card-text m-1"><strong>Technology:</strong> Angular, Node</p>
                                    </div>
                                    <hr>
                                    <div class="text-center mt-2">
                                        <a href="#" class="btn btn-outline-dark card-link">Mark as Unavailable</a> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!--Job Form Modal-->
        <div class="modal fade" id="add-a-job" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add a Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="#">
                        <div class="form-group">
                            <label for="emp-title">Title</label>
                            <textarea class="form-control" id="emp-title" name="emp-title" rows="2" placeholder="Job Title (E.g. Urgent Requirement for Project Manager)" maxlength="200" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="emp-designation">Designation</label>
                            <input type="text" class="form-control" id="emp-designation" name="emp-designation" placeholder="Enter Designation (E.g. Project Manager)" required>
                        </div>

                        <div class="form-group">
                            <label for="emp-description">Description</label>
                            <textarea class="form-control" id="emp-description" name="emp-description" rows="4" placeholder="Describe the job in brief" maxlength="1500" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="emp-requirement">Requirements</label>
                            <textarea class="form-control" id="emp-requirement" name="emp-requirement" rows="4" placeholder="State the requirements for the job" maxlength="1500" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="emp-salary">CTC</label>
                            <input type="number" class="form-control" id="emp-salary" name="emp-salary" placeholder="Enter CTC in LPA" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="emp-experience">Experience Required</label>
                            <input type="number" class="form-control" id="emp-experience" name="emp-experience" placeholder="Enter Experience Required in years" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="emp-location">Location</label>
                            <input type="text" class="form-control" id="emp-location" name="emp-location" placeholder="Enter Location (E.g. Mumbai)" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                        <button type="submit" name="addJobBtn" id="addJobBtn" class="btn btn-outline-dark">Add</button>
                    </div>
                </form>
                </div>
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
        <?php
            if(isset($_REQUEST["failed"])) {
                if($_REQUEST["failed"] == true) {
                    echo "<script src='js/job-error.js'></script>";
                }
            }

            if(isset($_REQUEST["success"])) {
                if($_REQUEST["success"] == true) {
                    echo "<script src='../js/success.js'></script>";
                }
            }
        ?>
         <!-- jQuery first, then Popper.js, then Bootstrap JS -->
         <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>