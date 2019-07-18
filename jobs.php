<?php
    header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");

    require "connect.php";
    require "mail.php";
    session_start();
    $loggedin = false;
    $statement;
    $error;
    $applied;

    if(isset($_SESSION["loggedin"])) {
        if($_SESSION["loggedin"] == true) {
            $loggedin = true;
            $userType = $_SESSION["userType"];
        } else {
            header("Location: index.php");
        }
    }

    if(isset($_REQUEST["apply"])) {
        // Check if loggged in
        if(!$loggedin || $userType != "jobseeker") {
            header("Location: index.php?access=denied");
        } else {

            // Check if already applied
            $statement = $con->prepare("SELECT id FROM applications WHERE jsid = '".$_SESSION["id"]."' AND jobid = :jobid");
            $statement->execute(array("jobid" => $_REQUEST["apply"]));
            if(!$statement->rowCount()) {

                // Insert a new row if not
                $statement = $con->prepare("INSERT INTO applications(id, jobid, jsid, date, status) VALUES('".md5(time() . $_SESSION["id"])."',:jobid, '".$_SESSION["id"]."', :date, 'Applied')");

                if(!$statement->execute(array(
                    "jobid" => $_REQUEST["apply"],
                    "date" => date('Y-m-d')
                ))) { $error = true; } else {

                    $statement = $con->prepare("SELECT j.designation, e.cname FROM jobs j, employer e WHERE j.id = :id AND j.emp_id = e.id");
                    $statement->execute(array("id" => $_REQUEST["apply"]));
                    $data = $statement->fetch(PDO::FETCH_ASSOC);

                    $message = '<p class="p-2">Hello '.$_SESSION["name"].'! Your application for the position of
                    '.$data['designation'].' at '.$data['cname'].' has been successfully
                    sent. We will notify you when your application is approved.</p>';

                    if(!sendmail($_SESSION["email"], $_SESSION["name"], "Job Application Successful", $message, null, null, "Location: jobseeker/my-applications.php?application=successful")) { $error = true; }
                }
            } else {
                header("Location: jobseeker/my-applications.php?application=present");
            }
        }
    }

    if(isset($_POST["title"])) {

        // Search term specific SQL
        $title = $_POST["title"] == "" ? "%" : "%".$_POST["title"]."%";
        $designation = $_POST["designation"] == "" ? "%" : "%".$_POST["designation"]."%";
        $salary = $_POST["salary"] == "" ? "0" : $_POST["salary"];
        $experience = $_POST["experience"] == "" ? "99" : $_POST["experience"];
        $location = $_POST["location"] == "" ? "%" : "%".$_POST["location"]."%";

        $sql = "SELECT * FROM jobs WHERE title LIKE :title AND designation LIKE :designation AND salary >= :salary AND experience <= :experience AND location LIKE :location AND available = 'true'";

        $statement = $con->prepare($sql);
        $statement->execute(array(
            'title' => $title,
            'designation' => $designation,
            'salary' => $salary,
            'experience' => $experience,
            'location' => $location
        ));

    } else if(isset($_REQUEST["designation"])) {

        $designation = "%".$_REQUEST["designation"]."%";
        $sql = "SELECT * FROM jobs WHERE designation LIKE :designation AND available = 'true'";
        $statement = $con->prepare($sql);
        $statement->execute(array('designation' => $designation));

    } else if(isset($_REQUEST["technology"])) {
        $technology = "%".$_REQUEST["technology"]."%";
        $sql = "SELECT * FROM jobs WHERE available = 'true' AND id IN (SELECT job_id FROM jobtech WHERE technology LIKE :technology)";
        $statement = $con->prepare($sql);
        $statement->execute(array('technology' => $technology));

    } else {

        // Default SQL
        $sql = "SELECT * FROM jobs WHERE available = 'true'";
        $statement = $con->prepare($sql);
        $statement->execute();
    }

    $result = $statement->rowCount() > 0 ? $statement->fetchAll(PDO::FETCH_ASSOC) : null;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <link rel="stylesheet" href="css/main.css">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" />
        <link rel="stylesheet" href="css/main.css">
        <title>Search Jobs</title>
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

                <!--Before Login-->
                <?php
                    if($loggedin == false) {
                        echo '
                            <li><a href="#" data-toggle="modal" data-target="#login">Login</a></li>
                            <li><a href="#" data-toggle="modal" data-target="#register">Register</a></li>
                        ';
                    }
                ?>

                <!--After Login-->
                <?php
                    if($loggedin == true) {
                        if($userType == "jobseeker") {
                            echo '
                                <li><a href="#" data-toggle="modal" data-target="#profile">Profile</a></li>
                                <li><a href="jobseeker/edit-profile.php">Edit</a></li>
                                <li><a href="jobseeker/my-applications.php">Applications</a></li>
                                <li><a href="logout.php">Logout</a></li>
                            ';
                        } else {
                            echo '
                                <li><a href="#" data-toggle="modal" data-target="#employer-profile">Profile</a></li>
                                <li><a href="employer/manage-jobs.php">Manage Jobs</a></li>
                                <li><a href="logout.php">Logout</a></li>
                            ';
                        }
                    }
                ?>

                <!-- Common -->
                <li><a href="privacy-policy">Privacy Policy</a></li>
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
            <h2 class="brand"><a href="index.php">Job Portal</a></h2>

            <div class="btn-group dropleft align-self-end p-2 ml-auto">
                <!--Profile Link-->
                <button type="button" class="btn btn-sm btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                </button>
                <?php
                    if($loggedin == true) {
                        if($userType == "jobseeker") {
                            echo '
                                <div class="dropdown-menu">
                                    <!--Options-->
                                    <a class="dropdown-item disabled" href="#">'.$_SESSION["name"].'</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profile">Profile</a>
                                    <a class="dropdown-item" href="jobseeker/edit-profile.php">Edit</a>
                                    <a class="dropdown-item" href="logout.php">Logout</a>
                                </div>
                            ';
                        } else {
                            echo '
                                <div class="dropdown-menu">
                                    <!--Options-->
                                    <a class="dropdown-item disabled" href="#">'.$_SESSION["name"].'</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#employer-profile">Profile</a>
                                    <a class="dropdown-item" href="employer/edit-profile.php">Edit</a>
                                    <a class="dropdown-item" href="logout.php">Logout</a>
                                </div>
                            ';
                        }
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

        <!--Page Content-->
        <div class="page-container">
            <div class="content-wrap container-fluid">

                <!-- Messages -->
                <div class="alert alert-danger" role="alert" id="error" style="display: none;">
                    <p>The server encountered an error and your application was not sent. Try again.</p>
                </div>

                <!-- Content -->
                <div class="text-center pb-3">
                    <h2>Find Jobs</h2>
                </div>
                <div class="row justify-content-center">

                    <div class="col-md-2 m-2 bg-light" id="ad">
                        <p class="p-2 text-center"><- Advertisements -></p>
                    </div>

                    <!-- Job Card -->
                    <div class="col m-2 bg-light" id="job-container">

            <?php
                $counter = 0;
                if(!$result == null) {
                    foreach ($result as $row) {
                        $counter = $counter + 1;

                        // Fetch logo
                        $statement = $con->prepare("SELECT logo FROM employer WHERE id = '".$row["emp_id"]."'");
                        $statement->execute();
                        $logo = $statement->fetch(PDO::FETCH_ASSOC)["logo"];

                        // Fetch skills
                        $sql = "SELECT * FROM jobtech WHERE job_id = '".$row["id"]."'";
                        $statement = $con->prepare($sql);
                        $statement->execute();
                        $skills = $statement->fetchAll(PDO::FETCH_ASSOC);
                        if($statement->rowCount() == 0) $skills = array();

                        // Fetch company
                        $statement = $con->prepare("SELECT cname FROM employer WHERE id = '".$row["emp_id"]."'");
                        $statement->execute();
                        $company = $statement->fetch(PDO::FETCH_ASSOC)["cname"];

                        // Check if applied
                        if($loggedin) {
                            $statement = $con->prepare("SELECT id FROM applications WHERE jsid = '".$_SESSION["id"]."' AND jobid = :jobid");
                            $statement->execute(array("jobid" => $row["id"]));
                            $applied = $statement->rowCount() ? true : false;
                        }
            ?>

                        <div class="card m-3">
                            <div class="row no-gutters align-items-center">
                                <div class="col-md-4 text-center">

                                <?php
                                    if($logo == "") {
                                        echo '
                                            <img src="images\default-logo.png" width="200" height="200" alt="company logo">
                                        ';
                                    } else {
                                        echo '
                                            <img src="uploads\logo\\'.$logo.'" width="200" height="200" alt="company logo">
                                        ';
                                    }
                                ?>

                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">

                                    <?php
                                        echo '
                                            <h5 class="card-title">'.$row['title'].'</h5><hr>
                                        ';
                                    ?>
                                        <div class="d-flex flex-wrap mb-3">
                                                <?php
                                                    if(!count($skills)) {
                                                        echo '
                                                            <div class="py-1 pr-1">
                                                                <p class="card-text"><small><strong>Skills: </strong>N/A</small></p>
                                                            </div>
                                                        ';
                                                    } else {
                                                        echo '
                                                            <div class="py-1 pr-1">
                                                                <p class="card-text"><small><strong>Skills: </strong></small></p>
                                                            </div>
                                                        ';
                                                        foreach ($skills as $skill) {
                                                            echo '
                                                                <div class="p-1">
                                                                    <span class="badge badge-primary p-1">'.$skill['technology'].'</span>
                                                                </div>
                                                            ';
                                                        }
                                                    }
                                                ?>
                                        </div>

                                <?php
                                    echo '
                                        <div class="d-flex mb-3">
                                            <div>
                                                <p class="card-text"><small><strong>Company: </strong>'.$company.'</small></p>
                                            </div>
                                            <div class="ml-4">
                                                <p class="card-text"><small><strong>Designation: </strong>'.$row['designation'].'</small></p>
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3">
                                            <div>
                                                <p class="card-text"><small><strong>CTC: </strong>'.$row['salary'].' LPA</small></p>
                                            </div>
                                            <div class="ml-4">
                                                <p class="card-text"><small><strong>Experience: </strong>'.$row['experience'].' Years</small></p>
                                            </div>
                                            <div class="ml-4">
                                                <p class="card-text"><small><strong>Location: </strong>'.$row['location'].'</small></p>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-toggle="modal" data-target="#more'.$counter.'">More Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Details Modal -->
                        <div class="modal fade" id="more'.$counter.'">
                            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="job-title">'.$row['title'].'</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body container">
                                        <div class="d-flex flex-wrap">

                                        ';

                                            if(!count($skills)) {
                                                echo '
                                                    <div class="py-1 pr-1">
                                                        <p><small><strong>Skills: </strong>N/A</small></p>
                                                    </div>
                                                ';
                                            } else {
                                                echo '
                                                    <div class="py-1 pr-1">
                                                        <p><small><strong>Skills: </strong></small></p>
                                                    </div>
                                                ';
                                                foreach ($skills as $skill) {
                                                    echo '
                                                        <div class="p-1">
                                                            <span class="badge badge-primary p-1">'.$skill['technology'].'</span>
                                                        </div>
                                                    ';
                                                }
                                            }

                                    echo '
                                        </div>
                                        <div class="d-flex">
                                            <div>
                                                <p><small><strong>Company: </strong>'.$company.'</small></p>
                                            </div>
                                            <div class="ml-4">
                                                <p><small><strong>Designation: </strong>'.$row['designation'].'</small></p>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div>
                                                <p><small><strong>CTC: </strong>'.$row['salary'].' LPA</small></p>
                                            </div>
                                            <div class="ml-4">
                                                <p><small><strong>Experience: </strong>'.$row['experience'].' Years</small></p>
                                            </div>
                                            <div class="ml-4">
                                                <p><small><strong>Location: </strong>'.$row['location'].'</small></p>
                                            </div>
                                        </div>
                                            <p><small><strong>Description: </strong>'.$row['description'].'</small></p>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>';

                                    // Check if applied already
                                    if(isset($applied) && $applied) {
                                        echo '
                                            <button class="btn btn-primary" disabled>Applied</button>
                                        ';
                                    } else {
                                        echo '
                                            <a href="jobs.php?apply='.$row['id'].'" class="btn btn-primary">Apply</a>
                                        ';
                                    }

                                echo '
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';
                }
            } else {
                echo '
                    <div class="alert alert-secondary mt-3" role="alert">
                        <p>Your search did not return any results. <strong><a class="text-dark" href="index.php">Try again.</a></strong></p>
                    </div>
                ';
            }
        ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Job Seeker Profile Modal -->
        <div class="modal fade" id="profile">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Your Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body container">
                        <?php
                            $sql = "SELECT name, email, phone, address, fresher, present_company, designation, salary, experience, cv, verified FROM jobseeker WHERE id = '".$_SESSION["id"]."'";
                            $statement = $con->prepare($sql);
                            $statement->execute();
                            $row = $statement->fetch(PDO::FETCH_ASSOC);

                            $mPhone = $row['phone'] == '' ? 'N/A' : $row['phone'];
                            $mAddress = $row['address'] == '' ? 'N/A' : $row['address'];
                            $mFresher = $row['fresher'] == 'true' ? 'Yes' : ($row['fresher'] == 'false' ? 'No' : 'N/A');
                            $mPresentCompany = $row['present_company'] == '' ? 'N/A' : $row['present_company'];
                            $mDesignation = $row['designation'] == '' ? 'N/A' : $row['designation'];
                            $mSalary = $row['salary'] == '' ? 'N/A' : "&#8377; " . $row['salary'];
                            $mExperience = $row['experience'] == '' ? 'N/A' : $row['experience'] . " Years";
                            $mFile = $row['cv'] == '' ? 'none' : $row['cv'];

                            if($row["verified"] == "true") {
                                echo '
                                    <p><b><i class="fas fa-user"></i> Name: </b>'.$row['name'].' <i class="fas fa-check-circle text-success"></i></p>
                                ';
                            } else {
                                echo '
                                    <p><b><i class="fas fa-user"></i> Name: </b>'.$row['name'].' <small class="text-muted">(validation pending)</small></p>
                                ';
                            }

                            echo '

                                <p><b><i class="fas fa-phone"></i> Phone: </b>'.$mPhone.'</p>

                                <p><b><i class="fas fa-envelope"></i> Email: </b>'.$row['email'].'</p>

                                <p><b><i class="fas fa-map-marked-alt"></i> Address: </b>'.$mAddress.'</p>

                                <p><b><i class="fas fa-briefcase"></i> Fresher: </b>'.$mFresher.'</p>

                                <p><b><i class="fas fa-building"></i> Present Company: </b>'.$mPresentCompany.'</p>

                                <p><b><i class="fas fa-user-tag"></i> Designation: </b>'.$mDesignation.'</p>

                                <p><b><i class="fas fa-money-check-alt"></i> Salary: </b>'.$mSalary.'</p>

                                <p><b><i class="fas fa-chart-line"></i> Experience: </b>'.$mExperience.'</p>

                            ';

                            if($mFile == 'none') {
                                echo '<p><b><i class="far fa-file-alt"></i> CV: </b>None. Upload from Edit Profile page.</p>';
                            } else {
                                echo '<b><i class="far fa-file-alt"></i> CV: </b><a class="text-dark" href="uploads/cv/'.$mFile.'">View File</a>';
                            }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <a href="jobseeker/edit-profile.php" class="btn btn-outline-primary">Edit</a>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!--Login Form-->
        <div class="modal fade" id="login">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Login</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form id="loginForm" action="index.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control" name="type" required>
                                <option hidden>Login As</option>
                                <option value="employer">Employer</option>
                                <option value="jobseeker">Job Seeker</option>
                            </select>
                        </div>
                        <div class="form-group">
                        <label for="email" class="col-form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                        <label for="password" class="col-form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="loginbtn" name="loginbtn">Login</button>
                    </div>
                </form>
              </div>
            </div>
          </div>

        <!--Register Form-->
        <div class="modal fade" id="register">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Register as</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <a href="employer/register.php" class="btn btn-primary btn-lg mr-2">Employer</a>
                        <a href="jobseeker/register.php" class="btn btn-primary btn-lg">Job Seeker</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <!--Footer-->
        <div id="footer" class="footer bg-primary-dark">
            <div class="d-flex flex-row justify-content-center  mt-3">
                <!--Social Links-->
                <div class="p-2 "><a href="#"><i class="fab fa-facebook"></i></a></div>
                <div class="p-2 "><a href="#"><i class="fab fa-twitter"></a></i></div>
                <div class="p-2 "><a href="#"><i class="fab fa-linkedin"></a></i></div>
            </div>
            <div class="text-center mt-2">
            <a href="privacy-policy">Privacy Policy</a>
            </div>
        </div>

        <!-- Optional JavaScript -->
        <script src="js/nav.js"></script>
        <?php
            if($error) {
                echo '<script src="js/error.js"></script>';
            }
        ?>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>