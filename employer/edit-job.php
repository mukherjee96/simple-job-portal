<?php
header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");

require "../connect.php";
session_start();
$loggedin = false;
$error = false;
$job_id;

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

//Checking for rights
if (!isset($_REQUEST["job"])) {
    header("location:manage-jobs.php");
} else {
    $stmt = $con->prepare("SELECT emp_id FROM jobs WHERE id = :id");
    $stmt->execute(array('id' => $_REQUEST["job"]));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stmt->rowCount() || $result['emp_id'] != $_SESSION['id'])
        header('location: ../index.php');
    else
        $job_id = $_REQUEST["job"];
}

//Fetching Data
$sql = "SELECT title,designation,description,salary,experience,location FROM jobs WHERE id='$job_id'";
$statement = $con->prepare($sql);
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);

// Obtain Technologies
$sql = "SELECT technology FROM jobtech WHERE job_id = :job_id";
$statement = $con->prepare($sql);
$statement->execute(array("job_id" => $job_id));
$technologies = $statement->fetchAll(PDO::FETCH_ASSOC);

//Delete Job
if (isset($_POST["deleteBtn"])) {
    $statement = $con->prepare("DELETE FROM jobs WHERE id = :id");
    $statement->execute(array("id" => $job_id));
    $statement = $con->prepare("DELETE FROM jobtech WHERE job_id = :job_id");
    $statement->execute(array("job_id" => $job_id));
    header("location:manage-jobs.php");
}

//Updating Job
if (isset($_POST["title"])) {
    $title = $_POST["title"];
    $designation = $_POST["designation"];
    $description = $_POST["description"];
    $salary = $_POST["salary"];
    $experience = $_POST["experience"];
    $location = $_POST["location"];

    $technology = json_decode($_POST["skills"]);

    $sql = "UPDATE jobs SET title = :title, designation = :designation, description = :description, salary = :salary, experience = :experience, location = :location WHERE id = :id";
    $statement = $con->prepare($sql);
    if (!$statement->execute(array(
        'title' => $title,
        'designation' => $designation,
        'description' => $description,
        'salary' => $salary,
        'experience' => $experience,
        'location' => $location,
        'id' => $job_id
    ))) $error = true;

    // Delete existing job technologies
    $sql = "DELETE from jobtech WHERE job_id = '" . $job_id . "'";
    $statement = $con->prepare($sql);
    if (!$statement->execute()) $error = true;

    // Update job technologies
    $sql = "INSERT INTO jobtech(job_id, technology) VALUES('" . $job_id . "', :technology)";
    $statement = $con->prepare($sql);
    foreach ($technology as $tech) {
        if (!$statement->execute(array(
            'technology' => $tech
        ))) $error = true;
    }

    if ($error) {
        header("Location: edit-job.php?job=" . $job_id . "&error=true");
    } else {
        header('Location: manage-jobs.php?edit-success=true');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" />

    <link rel="stylesheet" href="../css/main.css">

    <title>Edit Job</title>
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
            <div class="alert alert-danger" role="alert" id="edit-failed" style="display:none;">
                <p>An error ocurred and the job was not edited. Please try again.</p>
            </div>

            <div class="text-center">
                <h2>Edit Job</h2>
            </div>

            <!-- Job edit form -->

            <div class="container mt-3">
                <form action="#" method="POST" id="jobAddForm">
                    <h4 class="mt-4">Job Details</h4>
                    <hr><br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="title">Title</label>
                            <?php
                            echo '
                                <input type="text" class="form-control" id="title" name="title" value= "' . $row["title"] . '" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="designation">Designation</label>
                            <?php
                            echo '
                                <input type="text" class="form-control" id="designation" name="designation" value="' . $row["designation"] . '" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <?php
                        echo '
                                <textarea class="form-control" id="description" name="description" rows="4" maxlength="1500" required>' . $row["description"] . '</textarea>
                            ';
                        ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="salary">CTC (in LPA)</label>
                            <?php
                            echo '
                                    <input type="number" class="form-control" id="salary" name="salary" min = 0 value="' . $row["salary"] . '" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="experience">Experience</label>
                            <?php
                            echo '
                                    <input type="number" class="form-control" id="experience" min = 0 max = 99 name="experience" value="' . $row["experience"] . '" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="location">Location</label>
                            <?php
                            echo '
                                    <input type="text" class="form-control" id="location" name="location" value="' . $row["location"] . '" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <h4 class="mt-4">Technologies</h4>
                    <hr><br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="skill-input" placeholder="Add Technology">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="skill-add">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="d-flex flex-wrap skill-border" id="skill-container">
                                <?php
                                foreach ($technologies as $technology) {
                                    echo '
                                            <div class="p-1"><a class="btn btn-sm btn-primary" href="#" role="button">' . $technology['technology'] . '</a></div>
                                        ';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-center"><small class="text-muted">Click on a technology to remove</small></div>
                    <div class="row justify-content-center mt-5">
                        <div class="col-sm-3 text-center">
                            <button type="submit" class="btn btn-block btn-primary mb-3" id="addJobBtn" name="addJobBtn">Apply Changes</button>
                            <a href="#" class="text-danger" data-toggle="modal" data-target="#deleteJob">Delete Job</a>
                        </div>
                    </div>

                    <div class="modal fade" id="deleteJob" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Delete Job</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to permanently delete this job?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                                    <button type="submit" id="deleteBtn" name="deleteBtn" class="btn btn-primary">Delete</button>
                                </div>
                            </div>
                        </div>
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
    <script src="js/employer.js"></script>
    <?php
    if (isset($_REQUEST["error"])) {
        if ($_REQUEST["error"] == true) {
            echo "<script src='../js/edit-failed.js'></script>";
        }
    }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>