<?php
    require "../connect.php";
    session_start();
    $loggedin = false;

    if(isset($_SESSION["loggedin"])) {
        if($_SESSION["loggedin"] == true) {
            $loggedin = true;
        }
    } else {
        header("location:../");
    }
    
    // Obtain Personal Details & Experience
    $sql = "SELECT name,email,phone,address,fresher,present_company,designation,salary,experience FROM jobseeker WHERE id = '".$_SESSION["id"]."';";
    $statement = $con->prepare($sql);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $checked = $result["fresher"] == "true" ? "true" : "false";

    // Obtain Educational Details
    $sql = "SELECT board,yop,marks FROM jstenth WHERE jsid = '".$_SESSION["id"]."';";
    $statement = $con->prepare($sql);
    $statement->execute();
    $secondary = $statement->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT board,stream,yop,marks FROM jstwelveth WHERE jsid = '".$_SESSION["id"]."';";
    $statement = $con->prepare($sql);
    $statement->execute();
    $hs = $statement->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT university,dept,yop,marks FROM jsug WHERE jsid = '".$_SESSION["id"]."';";
    $statement = $con->prepare($sql);
    $statement->execute();
    $ug = $statement->fetch(PDO::FETCH_ASSOC);

    if(isset($_POST["updatebtn"])){
        // Personal details
        $name = $_POST['name'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];

        // Secondary Education
        $sboard = $_POST["sboard"];
        $syop = $_POST["syop"];
        $smarks = $_POST["smarks"];
        
        // Higher Secondary Education
        $hsboard = $_POST["hsboard"];
        $stream = $_POST["stream"];
        $hsyop = $_POST["hsyop"];
        $hsmarks = $_POST["hsmarks"];

        // Undergraduation
        $university = $_POST["university"];
        $department = $_POST["department"];
        $ugyop = $_POST["ugyop"];
        $ugmarks = $_POST["ugmarks"];

        // Experience
        $fresher = isset($_POST["fresher"]) == true ? "true" : "false";
        if($fresher == "false"){
            $present_company = $_POST['present_company'];
            $designation = $_POST['designation'];
            $salary = $_POST['salary'];
            $experience = $_POST['experience'];
        }

        // Update personal details
        $con->beginTransaction();
        $sql = "UPDATE jobseeker SET name='".$name."', email='".$email."', address='".$address."', phone='".$phone."';";
        $response = $con->exec($sql);
        $con->commit();

        //Update Experience
        if($fresher == "false") {
            $con->beginTransaction();
            $sql = "UPDATE jobseeker SET fresher='".$fresher."', present_company='".$present_company."', designation='".$designation."', salary='".$salary."', experience='".$experience."' WHERE id = '".$_SESSION['id']."';";
            $response = $con->exec($sql);
            $con->commit();
        }

        // Update secondary education
        $con->beginTransaction();
        $sql = "UPDATE jstenth SET jsid='".$_SESSION["id"]."', board='".$sboard."', yop='".$syop."', marks='".$smarks." WHERE jsid = '".$_SESSION['id']."';";
        $response = $con->exec($sql);
        $con->commit();

        // Update higher secondary education
        $con->beginTransaction();
        $sql = "UPDATE jstwelveth SET jsid='".$_SESSION["id"]."', board='".$hsboard."', stream='".$stream."', yop='".$hsyop."', marks='".$hsmarks." WHERE jsid = '".$_SESSION['id']."';";
        $response = $con->exec($sql);
        $con->commit();

        // Update undergraduation details
        $con->beginTransaction();
        $sql = "UPDATE jsug SET jsid='".$_SESSION["id"]."', university='".$university."', dept='".$department."', yop='".$ugyop."', marks='".$ugmarks." WHERE jsid = '".$_SESSION['id']."';";
        $response = $con->exec($sql);
        $con->commit();
    }

    if(isset($_POST["deletebtn"])){
        $con->beginTransaction();
        $sql = "DELETE FROM jobseeker WHERE id = ".$_SESSION['id'].";";
        $response = $con->exec($sql);
        $con->commit();
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
                <?php
                    if($loggedin == true) {
                        echo '                 
                            <li><a href="logout.php">Logout</a></li>
                        ';
                    }
                ?>
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
            
            <?php
                if($loggedin == true) {
                    echo '
                        <div class="btn-group dropleft align-self-end p-2 ml-auto">
                            <!--Profile Link-->
                            <button type="button" class="btn btn-sm btn-round btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                            </button>
                            <div class="dropdown-menu">
                                <!--Options-->
                                <a class="dropdown-item disabled" href="#">'.$_SESSION["name"].'</a>
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
                <div class="text-center">
                    <h2>Edit Your Profile</h2>
                </div>

                <form action="#" method="POST" enctype="multipart/form-data">

                    <h4 class="mt-5">Personal Details</h4><hr><br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Name</label>
                            <?php
                                echo '
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="'.$result["name"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <?php
                                echo'
                                    <input type="Email" class="form-control" id="email" name="email" placeholder="Email" value="'.$result["email"].'" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <?php
                            echo'
                                <input type="text" class="form-control" id="address" name="address" placeholder="Residential Address" value="'.$result["address"].'" required>
                            ';
                        ?>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="phone">Phone Number</label>
                            <?php
                                echo'
                                    <input type="number" class="form-control" id="phone" name="phone" placeholder="Phone Number" value="'.$result["phone"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="website">Upload CV</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                    </div>

                    <h4 class="mt-5">Education</h4><hr><br>
                    <h5>Secondary Education</h5>
                    <div class="form-row pt-2 mb-3">
                        <div class="form-group col-md-4">
                            <label for="sboard">Board</label>
                            <?php
                                echo '
                                    <input type="text" class="form-control" id="sboard" name="sboard" placeholder="Board" value="'.$secondary["board"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="syop">Year of Passing</label>
                            <?php
                                echo'
                                    <input type="number" class="form-control" id="syop" name="syop" placeholder="Year of Passing" value="'.$secondary["yop"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="smarks">Marks Obtained (%)</label>
                            <?php
                                echo'
                                    <input type="nubmer" class="form-control" id="smarks" name="smarks" placeholder="Marks Obtained" value="'.$secondary["marks"].'" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <h5>Higher Secondary Education</h5>
                    <div class="form-row pt-2">
                        <div class="form-group col-md-6">
                            <label for="hsboard">Board</label>
                            <?php
                                echo '
                                    <input type="text" class="form-control" id="hsboard" name="hsboard" placeholder="Board" value="'.$hs["board"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stream">Stream</label>
                            <?php
                                echo '
                                    <input type="text" class="form-control" id="stream" name="stream" placeholder="Science/Commerce/Arts" value="'.$hs["stream"].'" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <div class="form-row pt-2 mb-3">
                        <div class="form-group col-md-6">
                            <label for="hsyop">Year of Passing</label>
                            <?php
                                echo'
                                    <input type="number" class="form-control" id="hsyop" name="hsyop" placeholder="Year of Passing" value="'.$hs["yop"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="hsmarks">Marks Obtained (%)</label>
                            <?php
                                echo'
                                    <input type="number" class="form-control" id="hsmarks" name="hsmarks" placeholder="Marks Obtained" value="'.$hs["marks"].'" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <h5>Undergraduation</h5>
                    <div class="form-row pt-2">
                        <div class="form-group col-md-6">
                            <label for="university">University</label>
                            <?php
                                echo '
                                    <input type="text" class="form-control" id="university" name="university" placeholder="University" value="'.$ug["university"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="department">Department</label>
                            <?php
                                echo '
                                    <input type="text" class="form-control" id="department" name="department" placeholder="Department" value="'.$ug["dept"].'" required>
                                ';
                            ?>
                        </div>
                    </div>
                    <div class="form-row pt-2">
                        <div class="form-group col-md-6">
                            <label for="ugyop">Year of Passing</label>
                            <?php
                                echo'
                                    <input type="number" class="form-control" id="ugyop" name="ugyop" placeholder="Year of Passing" value="'.$ug["yop"].'" required>
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="ugmarks">CGPA</label>
                            <?php
                                echo'
                                    <input type="nubmer" class="form-control" id="ugmarks" name="ugmarks" placeholder="CGPA" value="'.$ug["marks"].'" required>
                                ';
                            ?>
                        </div>
                    </div>

                    <h4 class="mt-4">Experience</h4><hr>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="fresher" name="fresher">
                        <label class="custom-control-label" for="fresher">I am a fresher</label>
                    </div><br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="present_company">Present Company Name</label>
                            <?php
                                echo'
                                    <input type="text" class="form-control" id="present_company" name="present_company" value="'.$result["present_company"].'" placeholder="Company Name">
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="designation">Designation</label>
                            <?php
                                echo'
                                <input type="text" class="form-control" id="designation" name="designation" value="'.$result["designation"].'" placeholder="Designation">
                                ';
                            ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="salary">Salary</label>
                            <?php
                                echo'
                                <input type="number" class="form-control" id="salary" name="salary" value="'.$result["salary"].'"placeholder="Salary">
                                ';
                            ?>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="experience">Experience</label>
                            <?php
                                echo'
                                <input type="number" class="form-control" id="experience" name="experience" value="'.$result["experience"].'" placeholder="No. of years">
                                ';
                            ?>
                        </div>
                    </div>

                    <div class="row justify-content-center text-center mt-5">
                        <div class="col-4">
                            <button type="submit" name="updatebtn" class="btn btn-block btn-secondary mb-3">Update Profile</button>
                            <a href="#" class="text-danger" data-toggle="modal" data-target="#delete-profile">Delete Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!--Delete Profile Confirmation-->
            <div class="modal fade" id="delete-profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="deletebtn" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
    <script src="../js/app.js"></script>
    <script>
        const checkbox = document.getElementById('fresher');
        checkbox.addEventListener('click', function(e) {
            if(checkbox.checked === true) {
                document.getElementById('present_company').disabled = true;
                document.getElementById('designation').disabled = true;
                document.getElementById('salary').disabled = true;
                document.getElementById('experience').disabled = true;
                
            } else {
                document.getElementById('present_company').disabled = false;
                document.getElementById('designation').disabled = false;
                document.getElementById('salary').disabled = false;
                document.getElementById('experience').disabled = false;
            }
        });

        <?php
            if($checked)
                echo "document.getElementById('fresher').click()";
        ?>
    </script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>