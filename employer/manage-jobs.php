<?php
header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");

require "../connect.php";
session_start();
$loggedin = false;

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

if (isset($_POST['emp-title'])) {
    $error = false;

    $id = $_SESSION["id"];
    $job_id = substr(md5(time() . $id), 0, 6);
    $title = $_POST["emp-title"];
    $designation = $_POST["emp-designation"];
    $description = $_POST["emp-description"];
    $salary = $_POST["emp-salary"];
    $experience = $_POST["emp-experience"];
    $location = $_POST["emp-location"];
    $skills = json_decode($_POST['skills']);

    $sql = "INSERT INTO jobs(id, emp_id, title, designation, description, salary, experience, location,highlighted,available) VALUES(:id, '$id', :title, :designation, :description, :salary, :experience, :location, 'false','true')";

    $statement = $con->prepare($sql);
    if (!$statement->execute(array(
        'id' => $job_id,
        'title' => $title,
        'designation' => $designation,
        'description' => $description,
        'salary' => $salary,
        'experience' => $experience,
        'location' => $location
    ))) {
        $error = true;
    }

    $statement = $con->prepare("INSERT INTO jobtech(job_id, technology) VALUES('$job_id', :technology);");
    foreach ($skills as $skill) {
        $statement->execute(array('technology' => $skill));
    }

    if (!$error) {
        header("Location: manage-jobs.php?success=true");
    } else {
        header("Location: manage-jobs.php?failed=true");
    }
}

if (isset($_REQUEST["available"])) {
    $job_id = $_REQUEST["id"];

    $stmt = $con->prepare("SELECT emp_id FROM jobs WHERE id='$job_id'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['emp_id'] == $_SESSION['id']) {

        if ($_REQUEST["available"] == 'false')
            $sql = "UPDATE jobs SET available='false' WHERE id='$job_id'";
        else
            $sql = "UPDATE jobs SET available='true' WHERE id='$job_id'";
        $statement = $con->prepare($sql);
        $statement->execute();
    } else {
        header("Location: manage-jobs.php");
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
    <title>Manage Jobs</title>
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
            <button type="button" class="btn btn-sm btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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

    <div class="page-container">
        <div class="content-wrap container-fluid">

            <!-- Messages -->
            <div class="alert alert-success pb-3" role="alert" id="success" style="display:none;">
                <p>The job has been posted.</p>
            </div>

            <div class="alert alert-success pb-3" role="alert" id="edit-success" style="display:none;">
                <p>The job has been edited successfully.</p>
            </div>

            <div class="alert alert-danger" role="alert" id="jobNotAdded" style="display:none;">
                <p>An error ocurred and the job was not posted. Please try again.</p>
            </div>

            <!-- Post Job Button -->
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-sm-4">
                        <button class="btn btn-block btn-primary" data-toggle="modal" data-target="#add-a-job">Post a Job</button>
                    </div>
                </div>
            </div>

            <hr>
            <div class="justify-content-center text-center mb-4 pt-3">
                <h3>Jobs posted by you</h3>
            </div>
            <!-- Cards -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-8">

                        <!-- Modified -->
                        <?php
                        $sql = "SELECT id,title,designation,description,salary,experience,location FROM jobs WHERE emp_id='" . $_SESSION['id'] . "'";
                        $statement = $con->prepare($sql);
                        $statement->execute();

                        if (!$statement->rowCount()) {
                            echo '
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <p>No jobs have been added yet.</p>
                                        </div>
                                    </div>
                                ';
                        } else {
                            $count = 0;
                            $rows = $statement->fetchAll();
                            foreach ($rows as $row) {
                                $count = $count + 1;

                                $stmt = $con->prepare("SELECT technology FROM jobtech WHERE job_id='" . $row["id"] . "'");
                                $stmt->execute();
                                $tech = $stmt->fetchAll();

                                $tech_list = array();
                                foreach ($tech as $t) {
                                    array_push($tech_list, $t['technology']);
                                }
                                $allTech = implode(", ", $tech_list);

                                echo '
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <div class="d-flex">
                                                <div class="mr-auto p-2 "><h5 class="card-title">' . $row["title"] . '</h5></div>';

                                $st = $con->prepare("SELECT available FROM jobs WHERE id='" . $row["id"] . "'");
                                $st->execute();
                                $available = $st->fetch(PDO::FETCH_ASSOC);

                                if ($available['available'] == 'false') {

                                    echo ' <div class="p-2 "><a href="manage-jobs.php?available=true&id=' . $row['id'] . '" class="card-link text-dark" data-toggle="tooltip" data-placement="top" title="Mark as Available"><i class="fas fa-toggle-off"></i></a></div>';
                                }

                                if ($available['available'] == 'true') {

                                    echo ' <div class="p-2 "><a href="manage-jobs.php?available=false&id=' . $row['id'] . '" class="card-link text-dark" data-toggle="tooltip" data-placement="top" title="Mark as Unavailable"><i class="fas fa-toggle-on"></i></a></div>';
                                }

                                echo '<div class="p-2 "><a href="edit-job.php?job=' . $row['id'] . '" class="card-link"><i class="fas fa-edit text-dark"></i></a></div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row pl-3">
                                                <div class="col-sm-6 p-2">
                                                    <p><b>Designation:</b> ' . $row["designation"] . '</p>
                                                </div>
                                                <div class="col-sm-6 p-2">
                                                    <p><b>Location:</b> ' . $row["location"] . '</p>
                                                </div>
                                            </div>
                                            <div class="row pl-3">
                                                <div class="col-sm-6 p-2">
                                                    <p><b>CTC:</b> ' . $row["salary"] . ' LPA</p>
                                                </div>
                                                <div class="col-sm-6 p-2">
                                                    <p><b>Experience:</b> ' . $row["experience"] . ' year(s)</p>
                                                </div>
                                            </div>
                                            <div class="row pl-3">
                                                <p class="p-2"><b>Technology:</b> ' . $allTech . '</p>
                                            </div>
                                            <div class="collapse" id="moreDetails' . $count . '">
                                                <div class="pl-2">
                                                    <p><b>Description:</b> ' . $row["description"] . '</p>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <u><a class="text-dark" data-toggle="collapse" href="#moreDetails' . $count . '" id="more">More Details</a></u>
                                            </div>
                                            <hr>';

                                $sql = "SELECT jsid FROM applications WHERE jobid='" . $row['id'] . "' AND status = 'Applied'";
                                $stmt = $con->prepare($sql);
                                $stmt->execute();
                                $rcount = $stmt->rowCount();

                                echo '<div class="text-center mt-2">
                                                <a href="applicants.php?job=' . $row['id'] . '" class="btn btn-outline-primary">
                                                View Applicants <span class="badge badge-info ml-1">' . $rcount . '</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    ';
                            }
                        }
                        ?>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--Job Form Modal-->
    <div class="modal fade" id="add-a-job" tabindex="-1" role="dialog" aria-labelledby="Job Form Modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Add a Job</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="#" id="jobAddForm">
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
                            <label for="emp-salary">CTC</label>
                            <input type="number" class="form-control" id="emp-salary" name="emp-salary" min=0 placeholder="Enter CTC in LPA" required>
                        </div>

                        <div class="form-group">
                            <label for="emp-experience">Experience Required</label>
                            <input type="number" class="form-control" id="emp-experience" name="emp-experience" min="0" max="99" placeholder="Enter Experience Required in years" required>
                        </div>

                        <div class="form-group">
                            <label for="emp-location">Location</label>
                            <input type="text" class="form-control" id="emp-location" name="emp-location" placeholder="Enter Location (E.g. Mumbai)" required>
                        </div>

                        <label for="skill-input">Skills Required</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" placeholder="Add Technology" id="skill-input">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="skill-add">Add</button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap skill-border" id="skill-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                    <button type="submit" name="addJobBtn" id="addJobBtn" class="btn btn-primary">Add</button>
                </div>
                </form>
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
            <a href="../privacy-policy">Privacy Policy</a>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="../js/nav.js"></script>
    <?php
    if (isset($_REQUEST["failed"])) {
        if ($_REQUEST["failed"] == true) {
            echo "<script src='js/job-error.js'></script>";
        }
    }

    if (isset($_REQUEST["success"])) {
        if ($_REQUEST["success"] == true) {
            echo "<script src='../js/success.js'></script>";
        }
    }

    if (isset($_REQUEST["edit-success"])) {
        if ($_REQUEST["edit-success"] == true) {
            echo "<script src='../js/edit-success.js'></script>";
        }
    }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="js/employer.js"></script>
</body>

</html>