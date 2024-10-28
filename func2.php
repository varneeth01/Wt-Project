<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "127.0.0.1";
$username = "hmsuser";
$password = "varneeth";
$database = "myhmsdb";
session_start();

$con = mysqli_connect($host, $username, $password, $database, null, "/opt/lampp/var/mysql/mysql.sock");
if (isset($_POST['patsub1'])) {
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $gender = $_POST['gender'];
  $email = $_POST['email'];
  $contact = $_POST['contact'];
  $password = $_POST['password'];
  $cpassword = $_POST['cpassword'];
  $patAddress = $_POST['patAddress'];
  $lat = isset($_POST['lat']) && !empty($_POST['lat']) ? $_POST['lat'] : null;
  $lng = isset($_POST['lng']) && !empty($_POST['lng']) ? $_POST['lng'] : null;

  if ($lat === null || $lng === null) {
      echo "<script>alert('Latitude and Longitude cannot be null.');</script>";
      return;
  }

  if ($password == $cpassword) {
      $stmt = $con->prepare("INSERT INTO patreg (fname, lname, gender, email, contact, password, cpassword, patAddress, lat, lng) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      
      $stmt->bind_param("ssssssssss", $fname, $lname, $gender, $email, $contact, $password, $cpassword, $patAddress, $lat, $lng);
      
      if ($stmt->execute()) {
          $_SESSION['username'] = $fname . " " . $lname;
          $_SESSION['fname'] = $fname;
          $_SESSION['lname'] = $lname;
          $_SESSION['gender'] = $gender;
          $_SESSION['contact'] = $contact;
          $_SESSION['email'] = $email;
          $_SESSION['patAddress'] = $patAddress;
          echo "<script>alert('You registered successfully!');</script>";
      } else {
          echo "<script>alert('Error: " . $stmt->error . "');</script>";
      }
      
      $stmt->close();
  } else {
      header("Location:error1.php");
  }
}



if (isset($_POST['update_data'])) {
    $contact = $_POST['contact'];
    $status = $_POST['status'];
    $query = "UPDATE appointmenttb SET payment='$status' WHERE contact='$contact';";
    $result = mysqli_query($con, $query);
    if ($result) {
        header("Location:updated.php");
    }
}

if (isset($_POST['doc_sub'])) {
    $name = $_POST['name'];
    $query = "INSERT INTO doctb(name) VALUES('$name')";
    $result = mysqli_query($con, $query);
    if ($result) {
        header("Location:adddoc.php");
    }
}

function display_admin_panel() {
    echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <a class="navbar-brand" href="#"><i class="fa fa-user-plus" aria-hidden="true"></i> Global Hospital</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
     <ul class="navbar-nav mr-auto">
       <li class="nav-item">
        <a class="nav-link" href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
      </li>
       <li class="nav-item">
        <a class="nav-link" href="#"></a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0" method="post" action="search.php">
      <input class="form-control mr-sm-2" type="text" placeholder="enter contact number" aria-label="Search" name="contact">
      <input type="submit" class="btn btn-outline-light my-2 my-sm-0 btn btn-outline-light" id="inputbtn" name="search_submit" value="Search">
    </form>
  </div>
</nav>
  </head>
  <style type="text/css">
    button:hover{cursor:pointer;}
    #inputbtn:hover{cursor:pointer;}
  </style>
  <body style="padding-top:50px;">
 <div class="jumbotron" id="ab1"></div>
   <div class="container-fluid" style="margin-top:50px;">
    <div class="row">
  <div class="col-md-4">
    <div class="list-group" id="list-tab" role="tablist">
      <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab" aria-controls="home">Appointment</a>
      <a class="list-group-item list-group-item-action" href="patientdetails.php" role="tab" aria-controls="home">Patient List</a>
      <a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab" aria-controls="profile">Payment Status</a>
      <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" role="tab" aria-controls="messages">Prescription</a>
      <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings" role="tab" aria-controls="settings">Doctors Section</a>
       <a class="list-group-item list-group-item-action" id="list-attend-list" data-toggle="list" href="#list-attend" role="tab" aria-controls="settings">Attendance</a>
    </div><br>
  </div>

  <div class="col-md-8">
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
        <div class="container-fluid">
          <div class="card">
            <div class="card-body">
              <center><h4>Create an appointment</h4></center><br>
              <form class="form-group" method="post" action="appointment.php">
                <div class="row">
                  <div class="col-md-4"><label>First Name:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control" name="fname"></div><br><br>
                  <div class="col-md-4"><label>Last Name:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control"  name="lname"></div><br><br>
                  <div class="col-md-4"><label>Email id:</label></div>
                  <div class="col-md-8"><input type="text"  class="form-control" name="email"></div><br><br>
                  <div class="col-md-4"><label>Contact Number:</label></div>
                  <div class="col-md-8"><input type="text" class="form-control"  name="contact"></div><br><br>
                  <div class="col-md-4"><label>Doctor:</label></div>
                  <div class="col-md-8">
                   <select name="doctor" class="form-control">
                     <?php display_docs();?>
                    </select>
                  </div><br><br>
                  <div class="col-md-4"><label>Payment:</label></div>
                  <div class="col-md-8">
                    <select name="payment" class="form-control" >
                      <option value="" disabled selected>Select Payment Status</option>
                      <option value="Paid">Paid</option>
                      <option value="Pay later">Pay later</option>
                    </select>
                  </div><br><br><br>
                  <div class="col-md-4">
                    <input type="submit" name="entry_submit" value="Create new entry" class="btn btn-primary" id="inputbtn">
                  </div>
                  <div class="col-md-8"></div>                  
                </div>
              </form>
            </div>
          </div>
        </div><br>
      </div>
      <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
        <div class="container-fluid">
          <h4>Payment Status</h4><br>
          <form class="form-group" method="post" action="">
            <div class="row">
              <div class="col-md-4"><label>Contact Number:</label></div>
              <div class="col-md-8"><input type="text" class="form-control" name="contact"></div><br><br>
              <div class="col-md-4"><label>Status:</label></div>
              <div class="col-md-8"><input type="text" class="form-control" name="status"></div><br><br>
              <div class="col-md-4"><input type="submit" name="update_data" value="Update" class="btn btn-primary" id="inputbtn"></div>
            </div>
          </form>
        </div>
      </div>
      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
        <div class="container-fluid">
          <h4>Prescription</h4><br>
          <!-- Add prescription form here -->
        </div>
      </div>
      <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
        <div class="container-fluid">
          <h4>Doctors Section</h4><br>
          <form class="form-group" method="post" action="">
            <div class="row">
              <div class="col-md-4"><label>Doctor Name:</label></div>
              <div class="col-md-8"><input type="text" class="form-control" name="name"></div><br><br>
              <div class="col-md-4"><input type="submit" name="doc_sub" value="Add Doctor" class="btn btn-primary" id="inputbtn"></div>
            </div>
          </form>
        </div>
      </div>
      <div class="tab-pane fade" id="list-attend" role="tabpanel" aria-labelledby="list-attend-list">
        <div class="container-fluid">
          <h4>Attendance</h4><br>
          <!-- Add attendance form here -->
        </div>
      </div>
    </div>
  </div>
</div>
</div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3oAi8I+8xS0U8zY5Fgt7Uq6e1o6H8r2t2MchG+34zD0Yb5dH1eW+7mF58N8P+3" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-6nqsOt2VxZ4P7Dp/UxEdTo1ty9kZfDr6gu7MZQaW+5g4j+P7hA+j3E2Y2WxM5VccP" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-Gn5384xqQ1aoWXA+058R/c1bK9f4fWg4dYcX2XtlB5UigN6fdb7bB5E/ua8w/9Th" crossorigin="anonymous"></script>
  </body>
</html>';
}

function display_docs() {
    global $con;
    $query = "SELECT * FROM doctb";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
    }
}

display_admin_panel();
?>
