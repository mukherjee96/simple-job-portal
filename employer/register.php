<?php
    require '../connect.php';
    session_start();
    if(isset($_SESSION["loggedin"])) {
        if($_SESSION["loggedin"] == true)
            echo "<script>window.location.href='../'</script>";
    } else {
        if(isset($_POST['name'])) {

            // Company Detail
            $cname = $_POST['cname'];
            $cemail = $_POST['cemail'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];
            $website = $_POST['website'];
            $sector = $_POST['sector'];
            $formed = $_POST['formed'];
            $no_of_emp = $_POST['no_of_emp'];
            $type = $_POST['type'];
            $pan = $_POST['pan'];

            // Personal Detail
            $name = $_POST['name'];
            $email = $_POST['email'];
            $pass = $_POST['password'];
            $cpass = $_POST['cpassword'];

            if($pass == $cpass) {

                $pass = password_hash($pass, PASSWORD_DEFAULT);
                $sql = "INSERT INTO employer(id, cname, rname, sector, formed, pan, type, address, phone, remail, cemail, website, no_of_emp, password) VALUES (:id, :cname, :rname, :sector, :formed, :pan, :type, :address, :phone, :remail, :cemail, :website, :no_of_emp, '$pass');";
                $statement = $con->prepare($sql);

                if($statement->execute(array(
                    'id' => md5($email . time()),
                    'cname' => $cname,
                    'rname' => $name,
                    'sector' => $sector,
                    'formed' => $formed,
                    'pan' => $pan,
                    'type' => $type,
                    'address' => $address,
                    'phone' => $phone,
                    'remail' => $email,
                    'cemail' => $cemail,
                    'website' => $website,
                    'no_of_emp' => $no_of_emp
                ))) {
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
            <h2 class="brand"><a href="../">Job Portal</a></h2>

            <div class="btn-group dropleft align-self-end p-2 ml-auto">
                <!--Profile Link-->
                <button type="button" class="btn btn-sm btn-round dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    <h2>Register as Employer</h2>
                </div>

                <form action="#" method="POST">
                    <h4>Company Details</h4><hr><br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="cname">Company Name</label>
                            <input type="text" class="form-control" id="cname" name="cname" placeholder="Company Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cemail">Company Email</label>
                            <input type="email" class="form-control" id="cemail" name="cemail" placeholder="Company Email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="phone">Phone Number</label>
                            <input type="number" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="website">Company Website</label>
                            <input type="url" class="form-control" id="website" name="website" placeholder="Company Website" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="sector">Company Work Sector</label>
                            <input type="text" class="form-control" id="sector" name="sector" placeholder="Company Work Sector" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="formed">Company Formed</label>
                            <input type="number" class="form-control" id="formed" name="formed" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="no_of_emp">Number of Employees</label>
                            <input type="number" class="form-control" id="no_of_emp" name="no_of_emp" placeholder="Number of Employees" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="type">Company Type</label>
                            <select id="type" class="form-control" name="type" required>
                                <option hidden>Select Type</option>
                                <option value="Pvt Ltd">Pvt Ltd.</option>
                                <option value="LLP">LLP</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="pan">Company PAN</label>
                            <input type="text" class="form-control" id="pan" name="pan" placeholder="Company PAN" required>
                        </div>
                    </div>
                    <h4 class="mt-3">Personal Details</h4><hr><br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="Email" class="form-control" id="email" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="cpassword">Confirm Password</label>
                            <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Confirm Password" required>
                        </div>
                    </div><br>
                    <div class="row justify-content-center mt-4">
                        <div class="col-4">
                            <button type="submit" class="btn btn-block btn-primary">Register</button>
                        </div>
                    </div>
                </form>
                <div class="alert alert-danger fade mt-5" role="alert" id="alert">
                    Passwords do not match!
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
    <script>
        const pass = document.getElementById('password');
        const cpass = document.getElementById('cpassword');
        const form = document.getElementById('form');
        const alert = document.getElementById('alert');
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
                alert.classList.add('show');
                return false;
            } else {
                form.submit();
            }
        });
    </script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>