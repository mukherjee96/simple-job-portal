<?php
    require '../connect.php';
    session_start();
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
        echo "<script>window.location.href='../'</script>";
    } else {
        if(isset($_POST['name'])) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $pass = $_POST['password'];
            $cpass = $_POST['cpassword'];

            if($pass == $cpass) {
                
                $pass = password_hash($pass, PASSWORD_DEFAULT);
                $con->beginTransaction();
                $sql = "INSERT INTO jobseeker(name, email, password, verified) VALUES ('$name', '$email', '$pass', 'false');";
                $response = $con->exec($sql);
                $con->commit();

                $sql = "SELECT id FROM jobseeker WHERE email = '".$email."';";
                $statement = $con->prepare($sql);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);

                $con->beginTransaction();
                $sql = "INSERT INTO jstenth(jsid) VALUES ('".$result["id"]."');";
                $response = $con->exec($sql);
                $con->commit();

                $con->beginTransaction();
                $sql = "INSERT INTO jstwelveth(jsid) VALUES ('".$result["id"]."');";
                $response = $con->exec($sql);
                $con->commit();

                $con->beginTransaction();
                $sql = "INSERT INTO jsug(jsid) VALUES ('".$result["id"]."');";
                $response = $con->exec($sql);
                $con->commit();

                if($response) {
                    echo "<script>window.location.href='../index.php?account_created=true'</script>";
                } else {
                    $error = true;
                }
            } else {
                $alert = true;
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
                <div class="alert alert-danger fade mt-5" role="alert" id="alert">
                    Passwords do not match!
                </div>
                <div class="alert alert-danger mt-5" role="alert" id="errorAlert" style="display:none;">
                    Something went wrong. Try again.
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
    <script src="../js/app.js"></script>
    <script>
        const pass = document.getElementById('password');
        const cpass = document.getElementById('cpassword');
        const form = document.getElementById('form');
        const alert = document.getElementById('alert');
        const errorAlert = document.getElementById('errorAlert');

        cpass.addEventListener('keyup', function(e) {
            if(pass.value !== cpass.value) {
                cpass.classList.add('is-invalid');
                pass.classList.add('is-invalid');
            } else {
                cpass.classList.remove('is-invalid');
                pass.classList.remove('is-invalid');
            }
        });
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if(pass.value !== cpass.value) {
                alert.classList.add("show");
                return false;
            } else {
                form.submit();
            }
        });
    </script>
    <?php
        if($alert == true) {
            echo '<script>alert.classList.add("show");</script>';
        } else if($error == true) {
            echo '<script>errorAlert.classList.add("show");</script>';
        }
    ?>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>