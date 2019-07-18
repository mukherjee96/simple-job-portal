<!doctype html>
<html lang="en">

<head>
   <!-- Required meta tags -->
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

   <title>Admin Panel</title>
</head>

<body>
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">Admin Panel</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
         <div class="navbar-nav">
            <a class="nav-item nav-link" href="index.php?mode=jobseeker">Job Seekers</a>
            <a class="nav-item nav-link" href="index.php?mode=highlighted-job">Highlight Jobs</a>
            <a class="nav-item nav-link" href="mail-manage.php">Mail Management</a>
            <a class="nav-item nav-link" href="../logout.php">Logout</a>
         </div>
      </div>
   </nav>

   <!-- JOB SEEKER MANAGEMENT -->
   <?php
   if ($mode == 'jobseeker') {
      ?>
      <div class="container-fluid mt-4">
         <div class="d-flex mb-3">
            <div class="mr-auto p-2">
               <h4>Job Seeker Management</h4>
            </div>
            <div class="p-2 w-50"><input type="text" class="form-control" name="jobseeker-search" id="jobseeker-search" placeholder="Search by Name or Email"></div>
         </div>

         <div class="table-responsive">
            <table class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th scope="col">#</th>
                     <th scope="col">Name</th>
                     <th scope="col">Email</th>
                     <th scope="col">Phone</th>
                     <th scope="col" class="text-center">Access</th>
                  </tr>
               </thead>
               <tbody>

                  <!-- PHP GENERATES -->
                  <?php
                  if (!isset($_REQUEST['search'])) {
                     $sql = "SELECT name,email,phone,verified
                          FROM jobseeker";
                  } else {
                     $search = $_REQUEST['search'];
                     $sql = "SELECT name,email,phone,verified
                  FROM jobseeker
                  WHERE name LIKE '%" . $search . "%'
                     OR email LIKE '%" . $search . "%'";
                  }
                  $statement = $con->prepare($sql);
                  $statement->execute();
                  $rows = $statement->fetchAll();

                  $slno = 0;
                  foreach ($rows as $row) {
                     $slno = $slno + 1;
                     echo '
                  <tr>
                     <th scope="row">' . $slno . '</th>
                     <td>' . $row['name'] . '</td>
                     <td>' . $row['email'] . '</td>
                     <td>' . $row['phone'] . '</td>';

                     if ($row['verified'] == 'false') {
                        echo '<td class="text-center">
                           <a href="index.php?grant=true&email=' . $row['email'] . '" class="btn btn-sm btn-primary">Grant</a>
                           <a href="index.php?delete=true&email=' . $row['email'] . '" class="btn btn-sm btn-danger">Delete</a>
                        </td>';
                     } else {
                        echo '<td class="text-center">
                              <a href="index.php?revoke=true&email=' . $row['email'] . '" class="btn btn-sm btn-primary">Revoke</a>
                              <a href="index.php?delete=true&email=' . $row['email'] . '" class="btn btn-sm btn-danger">Delete</a>
                           </td>';
                     }
                     echo '</tr>';
                  }
               }  ?>
            </tbody>
         </table>
      </div>
   </div>

   <!-- /JOB SEEKER MANAGEMENT -->

   <!-- HIGHLIGHT JOBS -->

   <?php
   if ($mode == 'highlighted-job') {
      ?>

      <div class="container-fluid mt-4">
         <div class="d-flex mb-3">
            <div class="mr-auto p-2">
               <h4>Highlight Jobs</h4>
            </div>
            <div class="p-2 w-50"><input type="text" class="form-control" name="job-search" id="job-search" placeholder="Search by Designation or Skill"></div>
         </div>

         <div class="table-responsive">
            <table class="table table-hover table-bordered">
               <thead>
                  <tr>
                     <th scope="col">#</th>
                     <th scope="col">Title</th>
                     <th scope="col">Designation</th>
                     <th scope="col">Skills Required</th>
                     <th scope="col">Employer</th>
                     <th scope="col" class="text-center">Action</th>
                  </tr>
               </thead>
               <tbody>
                  <!-- PHP GENERATES -->
                  <?php
                  if (!isset($_REQUEST['search'])) {
                     $sql = "SELECT j.id,j.title,j.designation,j.highlighted,e.cname
                        FROM jobs j,employer e
                        WHERE j.emp_id = e.id";
                  } else {
                     $search = $_REQUEST['search'];
                     $sql = "SELECT j.id,j.title,j.designation,j.highlighted,t.technology,e.cname
                        FROM jobs j, jobtech t, employer e
                        WHERE j.emp_id = e.id AND j.id = t.job_id
                        AND j.designation LIKE '%" . $search . "%'
                           OR t.technology LIKE '%" . $search . "%'";
                  }
                  $statement = $con->prepare($sql);
                  $statement->execute();
                  $rows = $statement->fetchAll();

                  $slno = 0;
                  foreach ($rows as $row) {
                     $slno = $slno + 1;
                     if (!isset($_REQUEST['search'])) {
                        $id = $row['id'];

                        $stmt = $con->prepare("SELECT technology FROM jobtech WHERE job_id='" . $id . "'");
                        $stmt->execute();
                        $tech = $stmt->fetchAll();

                        $tech_list = array();
                        foreach ($tech as $t) {
                           array_push($tech_list, $t['technology']);
                        }
                        $allTech = implode(", ", $tech_list);
                     }

                     echo '
                           <tr>
                              <th scope="row">' . $slno . '</th>
                              <td>' . $row['title'] . '</td>
                              <td>' . $row['designation'] . '</td>';
                     if (!isset($_REQUEST["search"]))
                        echo '<td>' . $allTech . '</td>';
                     else
                        echo '<td>' . $row['technology'] . '</td>';
                     echo ' <td>' . $row['cname'] . '</td>';

                     if ($row['highlighted'] == 'false') {
                        echo '<td class="text-center">
                              <a href="index.php?highlight=true&job=' . $row['id'] . '" class="btn btn-sm btn-primary">Highlight</a>
                              </td>';
                     } else {
                        echo '<td class="text-center">
                                 <a href="index.php?highlight=false&job=' . $row['id'] . '" class="btn btn-sm btn-primary">Remove</a>
                              </td>';

                        echo '</tr>';
                     }
                  }
               } ?>
            </tbody>
         </table>
      </div>
   </div>

   <div id="searchForm" style="display:none;"></div>

   <!-- /HIGHLIGHT JOBS -->

   <!-- Optional JavaScript -->
   <script>
      const jobSeekerInput = document.getElementById('jobseeker-search');
      const jobInput = document.getElementById('job-search');
      const jobFormHolder = document.getElementById('searchForm');

      <?php
      if ($mode == 'jobseeker') {
         ?>
         // Job seeker
         jobSeekerInput.addEventListener('keyup', function(e) {
            if (e.keyCode == 13 && e.target.value !== '') {
               const form = document.createElement('form');
               form.id = 'js-search';
               form.action = 'index.php';
               form.method = 'POST';
               form.appendChild(jobSeekerInput);
               jobFormHolder.appendChild(form);
               form.submit();
            }
         });

      <?php } ?>

      <?php
      if ($mode == 'highlighted-job') {
         ?>

         //Job Search
         jobInput.addEventListener('keyup', function(e) {
            if (e.keyCode == 13 && e.target.value !== '') {
               const form = document.createElement('form');
               form.id = 'j-search';
               form.action = 'index.php';
               form.method = 'POST';
               form.appendChild(jobInput);
               jobFormHolder.appendChild(form);
               form.submit();
            }
         });

      <?php } ?>
   </script>
   <!-- jQuery first, then Popper.js, then Bootstrap JS -->
   <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>