<?php
    header("Content-Security-Policy: script-src 'self' https://code.jquery.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com");

    require '../connect.php';
    session_start();

    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
        header("Location: ../");
    } else {
        if(isset($_POST['name'])) {
            $name = $_POST['name'];
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            $sql = "SELECT email FROM jobseeker WHERE email = :email";
            $statement = $con->prepare($sql);
            $statement->execute(array(':email' => $email));

            if(!$statement->rowCount() && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $pass = $_POST['password'];
                $cpass = $_POST['cpassword'];
                
                if($pass == $cpass) {

                    $error = false;

                    $pass = password_hash($pass, PASSWORD_DEFAULT);
                    $id = md5(time() . $email);

                    $sql = "INSERT INTO jobseeker(id, name, email, password, verified) VALUES ('$id', :name, :email, '$pass', 'false');";
                    $statement = $con->prepare($sql);
                    if(!$statement->execute(array(
                        'name'=>$name, 
                        'email'=>$email
                    ))) $error = true;
    
                    $sql = "INSERT INTO jstenth(jsid) VALUES ('$id');";
                    $statement = $con->prepare($sql);
                    if(!$statement->execute())
                        $error = true;

                    $sql = "INSERT INTO jstwelveth(jsid) VALUES ('$id');";
                    $statement = $con->prepare($sql);
                    if(!$statement->execute())
                        $error = true;
    
                    $sql = "INSERT INTO jsug(jsid) VALUES ('$id');";
                    $statement = $con->prepare($sql);
                    if(!$statement->execute())
                        $error = true;
    
                    if(!$error) {
                        header("Location: ../index.php?account_created=true");
                    } else {
                        header("Location: register.php?error=true");
                    }
                } else {
                    header("Location: register.php?warning=true");
                }
            } else {
                header("Location: register.php?error=true");
            }
        }
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

    <title>Registration</title>
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
                <li><a href="../">Home</a></li>
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
            <h2 class="brand"><a href="../">Job Portal</a></h2>
            
            <div class="btn-group dropleft align-self-end p-2 ml-auto">
                <!--Profile Link-->
                <button type="button" class="btn btn-sm btn-round btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="../">Login</a>
                </div>
            </div>
        </div>

        <div class="page-container">
            <div class="content-wrap container">
                <div class="text-center mb-5">
                    <h2>Register as Job Seeker</h2>
                </div>
                
                <div class="mb-3">
                    <form action="#" method="POST" id="form">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="Email" class="form-control" id="email" name="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cpassword">Confirm Password</label>
                                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password">
                            </div>
                        </div>
                        <div class="row justify-content-center mt-4">
                            <div class="col-4">
                                <button type="submit" class="btn btn-block btn-secondary">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="alert alert-danger" role="alert" id="warning" style="display:none;">
                    Passwords do not match!
                </div>
                <div class="alert alert-danger" role="alert" id="error" style="display:none;">
                    <h4 class="alert-heading">Something went wrong. Try again.</h4>
                    <p>Probable causes:</p>
                    <ul>
                        <li>Account already exists.</li>
                        <li>Passwords do not match.</li>
                        <li>Invalid email.</li>
                    </ul>
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
    <script src="js/register.js"></script>
    <?php
        if(isset($_REQUEST["warning"])) {
            echo '<script src="../js/warning.js"></script>';
        } else if(isset($_REQUEST["error"])) {
            echo '<script src="../js/error.js"></script>';
        }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>