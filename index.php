<?php
header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");

require "connect.php";
session_start();
$loggedin = false;

// Load verification status

if (isset($_SESSION["loggedin"])) {
    if ($_SESSION["loggedin"] == true) {
        $loggedin = true;
        $userType = $_SESSION["userType"];
        if ($userType == "jobseeker") {
            $statement = $con->prepare("SELECT verified FROM jobseeker WHERE id = '" . $_SESSION["id"] . "'");
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $_SESSION["verified"] = $row["verified"];
        }
    }
}

// Manage logins

if (isset($_POST["loginbtn"])) {
    $type = $_POST["type"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if ($type == "jobseeker") {
        $sql = "SELECT id, name, password FROM jobseeker WHERE email = :email";
    } else if ($type == "employer") {
        $sql = "SELECT id, rname, password FROM employer WHERE remail = :email";
    }

    $statement = $con->prepare($sql);
    $statement->execute(array('email' => $email));
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $hash = $result["password"];

    if ($hash != "" && password_verify($password, $hash)) {
        $_SESSION["loggedin"] = true;
        $_SESSION["type"] = $type;
        $_SESSION["id"] = $result["id"];
        if ($type == "jobseeker") {
            $_SESSION["name"] = $result["name"];
            $_SESSION["userType"] = "jobseeker";
        } else {
            $_SESSION["name"] = $result["rname"];
            $_SESSION["userType"] = "employer";
        }
        $_SESSION["email"] = $email;
        header("Location: index.php?loggedin=true");
    } else {
        header("Location: index.php?error=true");
    }
}

// Fetch highlighted jobs

$sql = "SELECT id, designation FROM jobs WHERE highlighted = 'true'";
$statement = $con->prepare($sql);
$statement->execute();
$highlights = $statement->rowCount() > 0 ? $statement->fetchAll(PDO::FETCH_ASSOC) : null;

// Fetch highlighted technologies

$technologies = array();
if (!$highlights == null) {
    $sql = "SELECT technology FROM jobtech WHERE job_id = :job_id";
    $statement = $con->prepare($sql);
    foreach ($highlights as $highlight) {
        $statement->execute(array('job_id' => $highlight["id"]));
        if ($statement->rowCount()) {
            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
                array_push($technologies, $data["technology"]);
            }
        }
    }
} else {
    $technologies = null;
}

// Fetch highlighted BPOs

$sql = "SELECT title FROM jobs WHERE designation LIKE '%bpo%' AND highlighted = 'true'";
$statement = $con->prepare($sql);
$statement->execute();
$bpos = $statement->rowCount() > 0 ? $statement->fetchAll(PDO::FETCH_ASSOC) : null;
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
            <!--Before Login-->
            <?php
            if ($loggedin == false) {
                echo '
                            <li><a href="#" data-toggle="modal" data-target="#login">Login</a></li>
                            <li><a href="#" data-toggle="modal" data-target="#register">Register</a></li>
                            <li><a href="jobs.php">Browse Jobs</a></li>
                        ';
            }
            ?>

            <!--After Login-->
            <?php
            if ($loggedin == true) {
                if ($userType == "jobseeker") {
                    echo '
                                <li><a href="#" data-toggle="modal" data-target="#profile">Profile</a></li>
                                <li><a href="jobseeker/edit-profile.php">Edit</a></li>
                                <li><a href="jobseeker/my-applications.php">Applications</a></li>
                                <li><a href="jobs.php">Browse Jobs</a></li>
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

    <div class="d-flex align-items-center p-3 bg-grey">

        <!-- Brand Name -->
        <h2 class="brand">Job Portal</h2>

        <div class="btn-group dropleft align-self-end p-2 ml-auto">
            <!--Profile Link-->
            <button type="button" class="btn btn-sm btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle"></i>
            </button>
            <?php
            if ($loggedin == true) {
                if ($userType == "jobseeker") {
                    echo '
                                <div class="dropdown-menu">
                                    <!--Options-->
                                    <a class="dropdown-item disabled" href="#">' . $_SESSION["name"] . '</a>
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
                                    <a class="dropdown-item disabled" href="#">' . $_SESSION["name"] . '</a>
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

            <div class="row justify-content-center">
                <div class="col-11">

                    <!-- Messages -->

                    <div class="alert alert-success" role="alert" id="success" style="display: none;">
                        <p class="alert-text">Your account has been created successfully.</p>
                    </div>

                    <div class="alert alert-success" role="alert" id="loggedin" style="display: none;">
                        <p>Welcome back!</p>
                    </div>

                    <div class="alert alert-danger" role="alert" id="error" style="display: none;">
                        <p>Invalid email or password. Please try again.</p>
                    </div>

                    <div class="alert alert-warning" role="alert" id="warning" style="display: none;">
                        <p>Please <a href="#" class="alert-link" data-toggle="modal" data-target="#login">login</a> or <a href="#" class="alert-link" data-toggle="modal" data-target="#register">register</a> as job seeker to apply to jobs.</p>
                    </div>

                    <div class="alert alert-success" role="alert" id="loggedout" style="display: none;">
                        <p>You have been logged out.</p>
                    </div>

                    <!-- Search -->
                    <div id="search" class="bg-light mb-4 p-5">
                        <h1 class="text-center">Search Jobs</h1>
                        <form action="jobs.php" method="POST" class="mt-5" id="searchForm">
                            <div class="row justify-content-center mb-3">
                                <div class="col-md p-2">
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Job Title">
                                </div>
                                <div class="col-md p-2">
                                    <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation">
                                </div>
                                <div class="col-md p-2">
                                    <input type="number" class="form-control" id="salary" name="salary" placeholder="Salary">
                                </div>
                                <div class="col-md p-2">
                                    <input type="number" class="form-control" id="experience" name="experience" placeholder="Experience">
                                </div>
                                <div class="col-md p-2">
                                    <input type="text" class="form-control" id="location" name="location" placeholder="Location">
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-sm-3 p-3">
                                    <button id="search-btn" type="submit" name="search-btn" class="btn btn btn-block btn-primary">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Company Details -->
                    <div id="companies" class="pt-5 mb-4 bg-light">
                        <h1 class="text-center">Companies</h1>

                        <!--Carousel Code-->
                        <div id="carouselExampleInterval" class="carousel slide pt-4" data-ride="carousel">
                            <div class="carousel-inner">

                                <!--Carousel Items-->
                                <div class="carousel-item active px-4" data-interval="2000">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <div class="p-4">
                                            <img src="https://picsum.photos/id/1/500/500" height="500" width="500" class="img-fluid">
                                        </div>
                                        <div class="p-4">
                                            <img src="https://picsum.photos/id/2/500/500" height="500" width="500" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item px-4" data-interval="2000">
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <div class="p-4">
                                            <img src="https://picsum.photos/id/3/500/500" height="500" width="500" class="img-fluid">
                                        </div>
                                        <div class="p-4">
                                            <img src="https://picsum.photos/id/4/500/500" height="500" width="500" class="img-fluid">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleInterval" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleInterval" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>

                    <!-- Highlighted Job -->
                    <div id="highlighted-jobs" class="mb-4 bg-light p-5">
                        <h1 class="text-center">Highlighted Jobs</h1>
                        <div class="container mt-5">

                            <div class="row">
                                <div class="col-sm p-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-header">
                                            Designation
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            if ($highlights == null) {
                                                echo '
                                                            <p class="card-text">No highlights</p>
                                                        ';
                                            } else {
                                                foreach ($highlights as $highlight) {
                                                    echo '
                                                                <a class="text-dark" href="jobs.php?technology=' . $highlight['designation'] . '"><p class="card-text bg-light my-2 py-2">' . $highlight['designation'] . '</p></a>
                                                            ';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm p-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-header">
                                            Technology
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            if ($technologies == null) {
                                                echo '
                                                            <p class="card-text">No highlights</p>
                                                        ';
                                            } else {
                                                foreach ($technologies as $technology) {
                                                    echo '
                                                                <a class="text-dark" href="jobs.php?technology=' . $technology . '"><p class="card-text bg-light my-2 py-2">' . $technology . '</p></a>
                                                            ';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm p-3">
                                    <div class="card h-100 text-center">
                                        <div class="card-header">
                                            BPO
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            if ($bpos == null) {
                                                echo '
                                                            <p class="card-text">No highlights</p>
                                                        ';
                                            } else {
                                                foreach ($bpos as $bpo) {
                                                    echo '
                                                                <a class="text-dark" href="jobs.php?technology=' . $bpo['designation'] . '"><p class="card-text bg-light my-2 py-2">' . $bpo['designation'] . '</p></a>
                                                            ';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- About Us -->
                    <div id="about" class="bg-light p-5">
                        <h1 class="text-center">About Us</h1>
                        <p class="text-center mt-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque et consequat mi, non sagittis tortor. Donec posuere eros eu ornare finibus. Morbi aliquet dignissim cursus. Donec ultricies nibh blandit, volutpat dui eu, lacinia ante. Aenean fermentum vulputate lacus. Duis efficitur commodo tincidunt. Pellentesque eget enim at lorem interdum bibendum finibus id ante. Proin nec eros sem. Integer interdum vulputate leo, a fermentum tellus scelerisque sed. Sed lobortis est nunc, non consequat magna ornare sed. Sed mi ex, vehicula in ante sit amet, vestibulum suscipit risus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec ut dui mollis, faucibus ante id, pellentesque ante.</p>
                    </div>
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
                <form id="loginForm" action="#" method="POST">
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
                    $sql = "SELECT name, email, phone, address, fresher, present_company, designation, salary, experience, cv, verified FROM jobseeker WHERE id = '" . $_SESSION["id"] . "'";
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

                    if ($row["verified"] == "true") {
                        echo '
                                    <p><b><i class="fas fa-user"></i> Name: </b>' . $row['name'] . ' <i class="fas fa-check-circle text-success"></i></p>
                                ';
                    } else {
                        echo '
                                    <p><b><i class="fas fa-user"></i> Name: </b>' . $row['name'] . ' <small class="text-muted">(validation pending)</small></p>
                                ';
                    }

                    echo '

                                <p><b><i class="fas fa-phone"></i> Phone: </b>' . $mPhone . '</p>

                                <p><b><i class="fas fa-envelope"></i> Email: </b>' . $row['email'] . '</p>

                                <p><b><i class="fas fa-map-marked-alt"></i> Address: </b>' . $mAddress . '</p>

                                <p><b><i class="fas fa-briefcase"></i> Fresher: </b>' . $mFresher . '</p>

                                <p><b><i class="fas fa-building"></i> Present Company: </b>' . $mPresentCompany . '</p>

                                <p><b><i class="fas fa-user-tag"></i> Designation: </b>' . $mDesignation . '</p>

                                <p><b><i class="fas fa-money-check-alt"></i> Salary: </b>' . $mSalary . '</p>

                                <p><b><i class="fas fa-chart-line"></i> Experience: </b>' . $mExperience . '</p>

                            ';

                    if ($mFile == 'none') {
                        echo '<p><b><i class="far fa-file-alt"></i> CV: </b>None. Upload from Edit Profile page.</p>';
                    } else {
                        echo '<b><i class="far fa-file-alt"></i> CV: </b><a class="text-dark" href="uploads/cv/' . $mFile . '">View File</a>';
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

    <!-- Employer Profile Modal -->
    <div class="modal fade" id="employer-profile">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Recruiter Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body container">
                    <?php
                    $sql = "SELECT cname, rname, sector, formed, pan, type, address, phone, remail, cemail, website, no_of_emp, logo FROM employer WHERE id = '" . $_SESSION["id"] . "'";
                    $statement = $con->prepare($sql);
                    $statement->execute();
                    $row = $statement->fetch(PDO::FETCH_ASSOC);

                    if ($row['logo'] == "") {
                        echo '
                                <div class="text-center bg-light mb-3"><img src="images/default.png" alt="logo" class="p-2" height="200" width="200"></div>
                            ';
                    } else {
                        echo '
                                <div class="text-center bg-light mb-3"><img src="uploads/logo/' . $row['logo'] . '" alt="logo" class="p-2" height="200" width="200"></div>
                            ';
                    }

                    echo '

                            <b><i class="fas fa-user"></i> Recruiter</b><hr>

                            <p><b>Name: </b>' . $row['rname'] . '</p>

                            <p><b>Email: </b>' . $row['remail'] . '</p>

                            <br><b><i class="fas fa-building"></i> Company Details</b><hr>

                            <p><b>Name: </b>' . $row['cname'] . '</p>

                            <p><b>Type: </b>' . $row['type'] . '</p>

                            <p><b>Sector: </b>' . $row['sector'] . '</p>

                            <p><b>Formed: </b>' . $row['formed'] . '</p>

                            <p><b>PAN: </b>' . $row['pan'] . '</p>

                            <p><b>Address: </b>' . $row['address'] . '</p>

                            <p><b>Email: </b>' . $row['cemail'] . '</p>

                            <p><b>Phone: </b>' . $row['phone'] . '</p>

                            <p><b>Website: </b>' . $row['website'] . '</p>

                            <p><b>Number of Employees: </b>' . $row['no_of_emp'] . '</p>

                        ';
                    ?>
                </div>
                <div class="modal-footer">
                    <a href="employer/edit-profile.php" class="btn btn-outline-primary">Edit</a>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--Footer-->
    <div id="footer" class="footer bg-primary-dark">
        <div class="d-flex flex-row justify-content-center mt-3">
            <!--Social Links-->
            <div class="p-2 "><a href="#"><i class="fab fa-facebook"></i></a></div>
            <div class="p-2 "><a href="#"><i class="fab fa-twitter"></a></i></div>
            <div class="p-2 "><a href="#"><i class="fab fa-linkedin"></a></i></div>
        </div>
        <div class="text-center mt-2">
            <a href="#">Privacy Policy</a>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="js/nav.js"></script>
    <?php
    if (isset($_REQUEST["account_created"])) {
        if ($_REQUEST["account_created"] == true) {
            echo "<script src='js/success.js'></script>";
        }
    }

    if (isset($_REQUEST["error"])) {
        if ($_REQUEST["error"] == true) {
            echo "<script src='js/error.js'></script>";
        }
    }

    if (isset($_REQUEST["loggedout"])) {
        if ($_REQUEST["loggedout"] == true) {
            echo "<script src='js/logout.js'></script>";
        }
    }

    if (isset($_REQUEST["access"])) {
        if ($_REQUEST["access"] == "denied") {
            echo "<script src='js/warning.js'></script>";
        }
    }

    if (isset($_REQUEST["loggedin"])) {
        if ($_REQUEST["loggedin"] == true) {
            echo "<script src='js/loggedin.js'></script>";
        }
    }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>