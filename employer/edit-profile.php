<?php
header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com https://cdn.jsdelivr.net");

require "../connect.php";
session_start();
$loggedin = false;

if (isset($_SESSION["loggedin"])) {
    if ($_SESSION["loggedin"] == true && $_SESSION["userType"] == "employer") {
        $loggedin = true;
    }
} else {
    header("Location: ../");
}

$file = array('valid' => true, 'error' => '');

// Obtain Profile Details
$sql = "SELECT cname, rname, sector, formed, pan, type, address, phone, remail, cemail, website, no_of_emp FROM employer WHERE id = '" . $_SESSION["id"] . "'";
$statement = $con->prepare($sql);
$statement->execute();
$row = $statement->fetch(PDO::FETCH_ASSOC);

if (isset($_POST["cname"])) {

    $error = false;

    $cname = $_POST["cname"];
    $cemail = $_POST["cemail"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $website = $_POST["website"];
    $sector = $_POST["sector"];
    $formed = $_POST["formed"];
    $no_of_emp = $_POST["no_of_emp"];
    $type = $_POST["type"];
    $pan = $_POST["pan"];
    $rname = $_POST["rname"];
    $remail = $_POST["remail"];

    $sql = "UPDATE employer SET cname = :cname, cemail = :cemail, address = :address, phone = :phone, website = :website, sector = :sector, formed = :formed, no_of_emp = :no_of_emp, type = :type, pan = :pan, rname = :rname, remail = :remail WHERE id = '" . $_SESSION["id"] . "';";
    $statement = $con->prepare($sql);
    if (!$statement->execute(array(
        'cname' => $cname,
        'rname' => $rname,
        'sector' => $sector,
        'formed' => $formed,
        'pan' => $pan,
        'type' => $type,
        'address' => $address,
        'phone' => $phone,
        'remail' => $remail,
        'cemail' => $cemail,
        'website' => $website,
        'no_of_emp' => $no_of_emp
    ))) {
        $error = true;
    }

    // CV upload
    if (isset($_FILES["logo"]) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {

        $target_dir = "../uploads/logo/";

        $fileName = $_FILES['logo']['name'];
        $fileNameExploded = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameExploded));
        $newFileName = md5(time() . $fileName) . "." . $fileExtension;
        $target_file = $target_dir . $newFileName;

        // Delete old file if exists
        $sql = "SELECT logo FROM employer WHERE id = '" . $_SESSION["id"] . "';";
        $statement = $con->prepare($sql);
        $statement->execute();
        $oldFile = $statement->fetch(PDO::FETCH_ASSOC);
        if ($oldFile["logo"] != "") {
            unlink("../uploads/logo/" . $oldFile["logo"]);
        }

        // Check file size
        if ($_FILES["logo"]["size"] > 10000000) {
            $file['valid'] = false;
            $file['error'] = 'Your profile has been updated. The company logo was not uploaded since it is greater than 10MB.';
        }

        // Allow certain file formats
        if ($fileExtension != "jpg" && $fileExtension != "jpeg" && $fileExtension != "png") {
            $file['valid'] = false;
            $file['error'] = 'Your profile has been updated. The company logo was not uploaded since it is of an invalid format. Allowed formats: JPG, JPEG, PNG.';
        }

        if ($file['valid'] == true) {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $sql = "UPDATE employer SET logo ='" . $newFileName . "' WHERE id = '" . $_SESSION["id"] . "';";
                $statement = $con->prepare($sql);
                if (!$statement->execute())
                    $error = true;
            } else {
                header("Location: edit-profile.php?error=true");
            }
        } else {
            header("Location: edit-profile.php?warning=true");
        }
    }
    if (!$error) {
        header("Location: edit-profile.php?success=true");
    } else {
        header("Location: edit-profile.php?error=true");
    }
}

if (isset($_POST["deletebtn"])) {
    $sql = "DELETE FROM employer WHERE id = " . $_SESSION['id'] . ";";
    $statement = $con->prepare($sql);
    $statement->execute();
    header("Location: ../logout.php");
}

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.css" />

    <link rel="stylesheet" href="../css/main.css">

    <title>Edit Profile</title>
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
            <!--After Login-->
            <li><a href="../">Home</a></li>
            <?php
            if ($loggedin == true) {
                echo '
                            <li><a href="../logout.php">Logout</a></li>
                        ';
            }
            ?>
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

        <?php
        if ($loggedin == true) {
            echo '
                        <div class="btn-group dropleft align-self-end p-2 ml-auto">
                            <!--Profile Link-->
                            <button type="button" class="btn btn-sm btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div class="dropdown-menu">
                                <!--Options-->
                                <a class="dropdown-item disabled" href="#">' . $_SESSION["name"] . '</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../logout.php">Logout</a>
                            </div>
                        </div>
                    ';
        }
        ?>
    </div>

    <div class="page-container">
        <div class="content-wrap container">

            <div class="alert alert-success" role="alert" id="success" style="display: none;">
                <p>Your profile has been updated successfully.</p>
            </div>
            <div class="alert alert-danger" role="alert" id="error" style="display: none;">
                <p>The server encountered an error and your profile was not updated. Try again.</p>
            </div>
            <div class="alert alert-warning" role="alert" id="warning" style="display: none;">
                <p><?php echo $file['error']; ?></p>
            </div>

            <div class="text-center mb-5">
                <h2>Edit Your Profile</h2>
            </div>

            <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
                <h4>Company Details</h4>
                <hr><br>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="cname">Company Name</label>
                        <?php
                        echo '
                                    <input type="text" class="form-control" id="cname" name="cname" placeholder="Company Name" value="' . $row["cname"] . '" required>
                                ';
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cemail">Company Email</label>
                        <?php
                        echo '<input type="email" class="form-control" id="cemail" name="cemail" placeholder="Company Email" value="' . $row['cemail'] . '" required>';
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <?php
                    echo '<input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" value="' . $row['address'] . '" required>';
                    ?>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="phone">Phone Number</label>
                        <?php
                        echo '<input type="number" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="' . $row['phone'] . '" required>';
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="website">Company Website</label>
                        <?php
                        echo '<input type="url" class="form-control" id="website" name="website" placeholder="Company Website" value="' . $row['website'] . '" required>';
                        ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="sector">Company Work Sector</label>
                        <?php
                        echo '<input type="text" class="form-control" id="sector" name="sector" placeholder="Company Work Sector" value="' . $row['sector'] . '" required>';
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="logo">Company Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="logo" name="logo">
                            <label class="custom-file-label" for="logo">Choose file</label>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="formed">Company Formed</label>
                        <?php
                        echo '<input type="number" class="form-control" id="formed" name="formed" value="' . $row['formed'] . '" required>';
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="no_of_emp">Number of Employees</label>
                        <?php
                        echo '<input type="number" class="form-control" id="no_of_emp" name="no_of_emp" placeholder="Number of Employees" value="' . $row['no_of_emp'] . '" required>';
                        ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="type">Company Type</label>
                        <?php
                        echo '
                                    <select id="type" class="form-control" name="type" required>
                                        <option hidden>Select Type</option>
                                ';
                        if ($row['type'] == 'Pvt Ltd') {
                            echo '
                                        <option value="Pvt Ltd" selected>Pvt Ltd.</option>
                                        <option value="LLP">LLP</option></select>
                                    ';
                        } else {
                            echo '
                                        <option value="Pvt Ltd">Pvt Ltd.</option>
                                        <option value="LLP" selected>LLP</option></select>
                                    ';
                        }
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pan">Company PAN</label>
                        <?php
                        echo '<input type="text" class="form-control" id="pan" name="pan" placeholder="Company PAN" value="' . $row['pan'] . '" required>';
                        ?>
                    </div>
                </div>
                <h4 class="mt-3">Personal Details</h4>
                <hr><br>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Name</label>
                        <?php
                        echo '<input type="text" class="form-control" id="name" name="rname" placeholder="Name" value="' . $row["rname"] . '" required>';
                        ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email">Email</label>
                        <?php
                        echo '<input type="Email" class="form-control" id="email" name="remail" placeholder="Email" value="' . $row['remail'] . '" required>';
                        ?>
                    </div>
                </div><br>
                <div class="row justify-content-center text-center mt-5">
                    <div class="col-4">
                        <button type="submit" name="updatebtn" class="btn btn-block btn-primary mb-3" id="updatebtn">Update Profile</button>
                        <a href="#" class="text-danger" data-toggle="modal" data-target="#delete-profile">Delete Profile</a>
                    </div>
                </div>

                <!--Delete Profile Confirmation-->
                <div class="modal fade" id="delete-profile" tabindex="-1" role="dialog" aria-labelledby="delete-profile" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Delete Profile</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to permanently delete your profile?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                <button type="submit" name="deletebtn" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
    <script src="../js/fileinput.js"></script>
    <?php
    if (isset($_REQUEST['success'])) {
        echo '<script src="../js/success.js"></script>';
    }
    if (isset($_REQUEST['error'])) {
        echo '<script src="../js/error.js"></script>';
    }
    if (isset($_REQUEST['warning'])) {
        echo '<script src="../js/warning.js"></script>';
    }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js
"></script>
</body>

</html>