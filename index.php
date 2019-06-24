<?php
    require "connect.php";
    session_start();
    $loggedin = false;

    if(isset($_SESSION["loggedin"])) {
        if($_SESSION["loggedin"] == true) {
            $loggedin = true;
        }
    }

    if(isset($_POST["loginbtn"])) {
        $type = $_POST["type"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        if($type == "jobseeker") {
            $sql = "SELECT id, name, password FROM jobseeker WHERE email = '$email';";
        } else if($type == "employer") {
            $sql = "SELECT id, rname, password FROM employer WHERE remail = '$email';";
        }

        $statement = $con->prepare($sql);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $hash = $result["password"];

        if($hash != "" && password_verify($password, $hash)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["type"] = $type;
            $_SESSION["id"] = $result["id"];
            if($type == "jobseeker")
                $_SESSION["name"] = $result["name"];
            else
                $_SESSION["name"] = $result["rname"];
            $_SESSION["email"] = $email;
            $_SESSION["email"] = $email;
            echo "<script>window.location.href='index.php'</script>";
        } else {
            echo "<script>window.location.href='index.php?error=true'</script>";
        }
    }
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
                        echo '
                            <li><a href="#" data-toggle="modal" data-target="#profile">Profile</a></li>
                            <li><a href="jobseeker/edit-profile.php">Edit</a></li>                    
                            <li><a href="logout.php">Logout</a></li>
                        ';
                    }
                ?>

                <!-- Common -->
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
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profile">Profile</a>
                                <a class="dropdown-item" href="jobseeker/edit-profile.php">Edit</a>                    
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </div>
                    ';
                }
            ?>
        </div>
          
        <!--Page Content-->
        <div class="page-container">
            <div class="content-wrap container-fluid">

                <div class="row justify-content-center">
                    <div class="col-11">

                        <div class="alert alert-success" role="alert" id="success" style="display: none;">
                            <h4 class="alert-heading">Your account has been created.</h4>
                            <p>You will be able to login to your account once it is approved.</p>
                        </div>

                        <div class="alert alert-danger" role="alert" id="error" style="display: none;">
                            <p>Invalid email or password. Please try again.</p>
                        </div>

                        <div class="alert alert-success" role="alert" id="loggedout" style="display: none;">
                            <p>You have been logged out.</p>
                        </div>

                        <!-- Search -->
                        <div id="search" class="bg-light mb-4 p-5">
                            <h1 class="text-center">Search Jobs</h1>

                            <form action="#" method="POST" class="mt-5" id="searchForm">
                                <div class="row justify-content-center mb-3">
                                    <div class="col-md p-2">
                                        <input type="text" class="form-control" id="title" name="title" placeholder="Job Title" required>
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
                                        <button id="search-btn" type="submit" class="btn btn btn-block btn-secondary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    
                    <!-- Company Details -->
                        <div id="companies" class="mt-5 pt-5 bg-light">
                            <h1 class="text-center">Companies</h1>

                            <!--Carousel Code-->
                            <div id="carouselExampleInterval" class="carousel slide pt-5" data-ride="carousel">
                                <div class="carousel-inner">

                                    <!--Carousel Items-->
                                    <div class="carousel-item active px-4" data-interval="2000">
                                        <!--Carousel Content Code-->
                                        <div class="row">
                                            <div class="col-4 offset-1">                                		
                                                <img src="https://picsum.photos/700/600?random=1" class="d-block w-100" >
                                            </div>
                                            <div class="col-4 offset-2">                                		
                                                <img src="https://picsum.photos/700/600?random=2" class="d-block w-100" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item" data-interval="2000">
                                        <div class="row">
                                            <div class="col-4 offset-1">                                		
                                                <img src="https://picsum.photos/700/600?random=3" class="d-block w-100" >
                                            </div>
                                            <div class="col-4 offset-2">                                		
                                                <img src="https://picsum.photos/700/600?random=4" class="d-block w-100" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item" data-interval="2000">
                                        <div class="row">
                                            <div class="col-4 offset-1">                                		
                                                <img src="https://picsum.photos/700/600?random=5" class="d-block w-100" >
                                            </div>
                                            <div class="col-4 offset-2">                                		
                                                <img src="https://picsum.photos/700/600?random=6" class="d-block w-100" >
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
                        <div id="highlighted-jobs" class="mt-5 bg-light p-5">    
                            <h1 class="text-center">Highlighted Jobs</h1>
                            <div class="container mt-5">

                                <div class="row">
                                    <div class="col-sm p-3">
                                        <div class="card text-center">
                                            <div class="card-header">
                                                Designation
                                            </div>
                                            <div class="card-body">
                                                <!--Generated by PHP-->
                                                <a href="#">Lorem ipsum</a><br>
                                                <a href="#">Lorem ipsum</a><br>
                                                <a href="#">Lorem ipsum</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm p-3">
                                        <div class="card text-center">
                                            <div class="card-header">
                                                Technology
                                            </div>
                                            <div class="card-body">
                                                <!--Generated by PHP-->
                                                <a href="#">dolor sit</a><br>
                                                <a href="#">dolor sit</a><br>
                                                <a href="#">dolor sit</a>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <div class="col-sm p-3">
                                        <div class="card text-center">
                                            <div class="card-header">
                                                BPO
                                            </div>
                                            <div class="card-body">
                                                <!--Generated by PHP-->
                                                <a href="#">amet consectetur</a><br>
                                                <a href="#">amet consectetur</a><br>
                                                <a href="#">amet consectetur</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            
                            </div>      
                        </div>
                    
                    <!-- About Us -->
                        <div id="about" class="mt-5 bg-light p-5">
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
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary" id="loginbtn" name="loginbtn">Login</button>
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
                        <a href="employer/register.php" class="btn btn-secondary btn-lg mr-2">Employer</a>
                        <a href="jobseeker/register.php" class="btn btn-secondary btn-lg">Job Seeker</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Profile Modal -->
        <!-- Button trigger modal -->
        <div class="modal fade" id="profile">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Your Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <div class="modal-body container">
                    <h4>Name</h4>
                    <p>Aritra Mukherjee</p>
                    <h4>Phone</h4>
                    <p>9674303832</p>
                    <h4>Email</h4>
                    <p>aritramukherjee100@gmail.com</p>
                    <h4>Address</h4>
                    <p>Jadavpur, Kolkata, India</p>
                    <h4>Fresher</h4>
                    <p>No</p>
                    <h4>Present Company</h4>
                    <p>Microsoft</p>
                    <h4>Designation</h4>
                    <p>AI Engineer</p>
                    <h4>Salary</h4>
                    <p>$8000</p>
                    <h4>Experience</h4>
                    <p>3 Years</p>
                </div>
                <div class="modal-footer">
                    <a href="jobseeker/edit-profile.html" class="btn btn-secondary">Edit</a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
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
        <script src="js/app.js"></script>
        <script>
            const success = document.getElementById('success');
            const errorAlert = document.getElementById('error');
            const loggedout = document.getElementById('loggedout');
            <?php
                if(isset($_REQUEST["account_created"])) {
                    if($_REQUEST["account_created"] == true) {
                        echo "success.style.display = 'block';
                            setTimeout(() => {success.style.display = 'none'}, 4000);";
                    }
                }
                if(isset($_REQUEST["error"])) {
                    if($_REQUEST["error"] == true) {
                        echo "errorAlert.style.display = 'block';
                            setTimeout(() => {errorAlert.style.display = 'none'}, 3000);";
                    }
                }
                if(isset($_REQUEST["loggedout"])) {
                    if($_REQUEST["loggedout"] == true) {
                        echo "loggedout.style.display = 'block';
                        setTimeout(() => {loggedout.style.display = 'none'}, 3000);";
                    }
                }
            ?>
        </script>
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>