<?php
    require "../connect.php";
    session_start();
    if(isset($_POST['loginbtn'])){
        $statement = $con->prepare("SELECT username, password FROM admin");
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($_POST['username'] == $result['username'] && password_verify($_POST['password'], $result['password'])){
            $_SESSION['admin'] = true;
            header('location:index.php');
        }
        else{
            header('location:login.php?error=true');
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

    <link rel="stylesheet" href="styles.css">

    <title>Admin Panel</title>
  </head>
  <body>
    <div class="container-fluid">
        <div class="maximize-height row justify-content-center align-items-center">
            <div class="col-4 login p-5">
                <div class="text-center">
                    <i class="fas fa-user-circle h1"></i>
                </div>
                <h3 class="text-center pb-3">Please sign in</h3>
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                    <div class="row justify-content-center pt-3">
                        <div class="col-5">
                            <button type="submit" name="loginbtn" class="btn btn-block btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        <?php 
            if(isset($_REQUEST['error']))
                echo '
                    <div class="container">
                        <div class="alert alert-danger" role="alert" id="error">
                            <p>Invalid email or password. Please try again.</p>
                        </div>
                    </div>
                ';
        ?>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>