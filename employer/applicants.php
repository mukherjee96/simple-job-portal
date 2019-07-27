<?php
header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");

require "../connect.php";
require "../mail.php";
session_start();
$loggedin = false;
$error = false;

if (isset($_SESSION["loggedin"])) {
    if ($_SESSION["loggedin"] == true) {
        $loggedin = true;
        $userType = $_SESSION["userType"];
        if ($userType == 'jobseeker')
            header("location:../index.php");
    }
} else {
    header("location:../index.php");
}


if (!isset($_REQUEST["job"]))
    header("location:manage-jobs.php");

// $job_id = $_REQUEST["job"];
$stmt = $con->prepare("SELECT emp_id FROM jobs WHERE id = :id");
$stmt->execute(array('id' => $_REQUEST["job"]));
$employer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SESSION['id'] != $employer["emp_id"])
    header("location:manage-jobs.php");

// Approve or Reject Application
if (isset($_REQUEST["mode"]) && isset($_REQUEST["application"])) {
    $statement = $con->prepare("SELECT emp_id FROM jobs WHERE id = (SELECT jobid FROM applications WHERE id = :id) AND emp_id = :emp_id");
    $statement->execute(array(
        'id' => $_REQUEST["application"],
        'emp_id' => $_SESSION["id"]
    ));
    if ($statement->rowCount() == 0) {
        $error = true;
    } else {
        if ($_REQUEST["mode"] == 'approve') {
            $sql = "UPDATE applications SET status = 'Approved' WHERE id = :id";
            $stmt = $con->prepare($sql);
            if (!$stmt->execute(array('id' => $_REQUEST["application"]))) {
                $error = true;
            } else {
                $email_error = false;

                // Get job seeker info
                $statement = $con->prepare("SELECT js.name, js.email, js.phone, js.cv, j.designation, emp.cname FROM jobseeker js, jobs j, employer emp WHERE js.id = (SELECT jsid FROM applications WHERE id = :id) AND j.id = (SELECT jobid FROM applications WHERE id = :id) AND emp.id = :empid");
                $statement->execute(array(
                    "id" => $_REQUEST["application"],
                    "empid" => $_SESSION["id"]
                ));
                $data = $statement->fetch(PDO::FETCH_ASSOC);

                // Job seeker message
                $message = '
                    <p class="p-2">
                    Hello ' . $data['name'] . '! Your application for the designation of ' . $data['designation'] . ' at ' . $data['cname'] . ' has been approved!
                    </p>
                    ';
                if (!sendmail($data["email"], $data["name"], "Application Approved", $message, null, null, null)) {
                    $email_error = true;
                }

                // Employer message
                $message = '
                    <p class="p-2">Hello Mr./Ms. ' . $_SESSION['name'] . ',<br> this email contains information regarding the application for the designation of ' . $data['designation'] . ' at ' . $data['cname'] . ' that you have approved:</p>
                    <ul>
                    <li><b>Name:</b> ' . $data["name"] . '</li>
                    <li><b>Email:</b> ' . $data["email"] . '</li>
                    <li><b>Phone:</b> ' . $data["phone"] . '</li>
                    </ul><br>
                    <p>Please find the candidate\'s CV attached with this mail.</p>';

                if (!sendmail($_SESSION["email"], $_SESSION["name"], "Application Approved", $message, "../uploads/cv/" . $data["cv"], $data["name"], "Location: applicants.php?job=2")) {
                    $email_error = true;
                }

                if ($email_error) {
                    $sql = "UPDATE applications SET status = 'Applied' WHERE id = :id";
                    $stmt = $con->prepare($sql);
                    if (!$stmt->execute(array('id' => $_REQUEST["application"]))) {
                        $error = true;
                    }
                }
            }
        } else if ($_REQUEST["mode"] == 'reject') {
            $sql = "UPDATE applications SET status='Rejected' WHERE id=:id";
            $stmt = $con->prepare($sql);
            if (!$stmt->execute(array('id' => $_REQUEST["application"]))) {
                $error = true;
            } else {
                $email_error = false;

                // Get job seeker info
                $statement = $con->prepare("SELECT js.name, js.email, j.designation, emp.cname FROM jobseeker js, jobs j, employer emp WHERE js.id = (SELECT jsid FROM applications WHERE id = :id) AND j.id = (SELECT jobid FROM applications WHERE id = :id) AND emp.id = :empid");
                $statement->execute(array(
                    "id" => $_REQUEST["application"],
                    "empid" => $_SESSION["id"]
                ));
                $data = $statement->fetch(PDO::FETCH_ASSOC);

                // Job seeker message
                $message = '
                    <p class="p-2">
                    Hello ' . $data['name'] . ', your application for the designation of ' . $data['designation'] . ' at ' . $data['cname'] . ' has been rejected.
                    </p>
                    ';
                if (!sendmail($data["email"], $data["name"], "Application Rejected", $message, null, null, "Location: applicants.php?job=2")) {
                    $email_error = true;
                }

                if ($email_error) {
                    $sql = "UPDATE applications SET status = 'Applied' WHERE id = :id";
                    $stmt = $con->prepare($sql);
                    if (!$stmt->execute(array('id' => $_REQUEST["application"]))) {
                        $error = true;
                    }
                }
            }
        }
    }
}

$statement = $con->prepare("SELECT id, jsid, status FROM applications WHERE jobid = :job_id AND status = 'Applied'");
if (!$statement->execute(array('job_id' => $_REQUEST["job"]))) {
    $error = true;
} else {
    $applications = $statement->fetchAll();
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
                <li><a href="../privacy-policy">Privacy Policy</a></li>
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
            <h2 class="brand"><a href="../">S S Consulting Services LLP</a></h2>

            <div class="btn-group dropleft align-self-end p-2 ml-auto">
                <!--Profile Link-->
                <button type="button" class="btn btn-sm btn-round btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                </button>
                <?php
                if ($loggedin == true) {
                    echo '
                            <div class="dropdown-menu">
                                <!--Options-->
                                <a class="dropdown-item disabled" href="#">' . $_SESSION["name"] . '</a>
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

        <div class="page-container container-fluid">
            <div class="content-wrap">

                <!-- Messages -->
                <div class="alert alert-danger" role="alert" id="error" style="display: none;">
                    <p>Something went wrong. Please try again.</p>
                </div>

                <div class="mt-2 text-center">
                    <h3>Applicants</h3>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-center">
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">University</th>
                                <th scope="col">YOP</th>
                                <th scope="col">Designation</th>
                                <th scope="col">Current Salary (LPA)</th>
                                <th scope="col">Document</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            foreach ($applications as $application) {
                                if (!$statement->rowCount() == 0) {
                                    $sql = "SELECT j.id,j.name,j.fresher,j.present_company,j.designation,j.salary,j.experience,j.cv,u.university,u.yop FROM jobseeker j, jsug u WHERE j.id=:id AND u.jsid=:id";
                                    $stmt = $con->prepare($sql);
                                    if (!$stmt->execute(array(
                                        "id" => $application["jsid"]
                                    ))) {
                                        $error = true;
                                    } else {
                                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo '
                                            <tr class="text-center h5 font-weight-normal">
                                                <th scope="row">1</th>
                                                <td>' . $row["name"] . '</td>
                                                <td>' . $row["university"] . '</td>
                                                <td>' . $row["yop"] . '</td> ';

                                        if ($row["fresher"] == true) {
                                            echo '
                                                        <td>' . $row["designation"] . ' at ' . $row["present_company"] . '</td>
                                                        <td>' . $row["salary"] . '</td>
                                                        ';
                                        } else {
                                            echo '
                                                        <td>N/A</td>
                                                        <td>N/A</td>
                                                    ';
                                        }
                                        echo ' <td><a target="_blank" href="../uploads/cv/' . $row["cv"] . '"><i class="fas fa-file-download text-dark "></i></a></td>
                                                <td>
                                                    <div class="d-flex flex-row justify-content-center">

                                                        <a href="applicants.php?job=' . $_REQUEST["job"] . '&application=' . $application["id"] . '&mode=approve" class="text-success bg-light p-1" id="approve"><i class="fas fa-check-circle"></i></a>

                                                        <a href="applicants.php?job=' . $_REQUEST["job"] . '&application=' . $application["id"] . '&mode=reject" class="text-danger bg-light ml-1 p-1" id="reject"><i class="fas fa-ban"></i></a>

                                                    </div>
                                                </td>
                                            </tr>
                                        ';
                                    }
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>

                <?php
                if ($statement->rowCount() == 0) {
                    echo '
                            <div class="text-center py-3">No applications are pending approval.</div>
                        ';
                }
            }
            ?>

        </div>
    </div>

    <!-- Spinner -->
    <div class="modal" tabindex="-1" role="dialog" id="spinner">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <strong>Processing...</strong>
                        <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="text-center p-4"><small>This usually takes a few seconds. Candidate details will be mailed.</small></div>
                </div>
            </div>
        </div>
    </div>

    <!--Footer-->
    <div id="footer" class="footer bg-dark">
        <div class="d-flex flex-row justify-content-center  mt-3">
            <!--Social Links-->
            <div class="p-2 "><a href="#"><i class="fab fa-facebook"></i></a></div>
            <div class="p-2 "><a href="#"><i class="fab fa-twitter"></a></i></div>
            <div class="p-2 "><a href="#"><i class="fab fa-linkedin"></a></i></div>
        </div>
        <div class="text-center mt-2">
            <a href="../privacy-policy">Privacy Policy</a>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="../js/nav.js"></script>
    <?php
    if ($error) {
        echo "<script src='js/error.js'></script>";
    }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="js/applicants.js"></script>
</body>

</html>