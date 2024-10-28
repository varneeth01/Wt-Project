<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $host = "127.0.0.1";
// $username = "hmsuser";
// $password = "varneeth";
// $database = "myhmsdb";
// $socket = "/opt/lampp/var/mysql/mysql.sock";

// $con = mysqli_connect($host, $username, $password, $database,null, $socket);
// if (!$con) {
//     die("Connection failed:" . mysqli_connect_error());
// }

function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function logError($message) {
    error_log($message, 3, "error.log");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    if (isset($_POST['patsub1'])) {
        $fname = !empty($_POST['fname']) ? validateInput($_POST['fname']) : null;
        $lname = !empty($_POST['lname']) ? validateInput($_POST['lname']) : null;
        $email = !empty($_POST['email']) ? validateInput($_POST['email']) : null;
        $contact = !empty($_POST['contact']) ? validateInput($_POST['contact']) : null;
        $password = !empty($_POST['password']) ? password_hash(validateInput($_POST['password']), PASSWORD_DEFAULT) : null;
        $gender = !empty($_POST['gender']) ? validateInput($_POST['gender']) : null;
        $patAddress = !empty($_POST['patAddress']) ? validateInput($_POST['patAddress']) : null;

        if (!$fname || !$lname || !$email || !$contact || !$password || !$gender || !$patAddress) {
            logError("Patient registration error: Missing fields");
            echo "<script>alert('Please fill all fields.');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;
        }

        $stmt = $con->prepare("INSERT INTO patreg (fname, lname, email, contact, password, gender, patAddress) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fname, $lname, $email, $contact, $password, $gender, $patAddress);

        if ($stmt->execute()) {
            echo "<script>alert('Patient registration successful!');</script>";
            echo "<script>window.location.href = 'func.php';</script>";
        } else {
            logError("Patient registration error: " . $stmt->error);
            echo "<script>alert('Error during registration. Please try again.');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        $stmt->close();
    } elseif (isset($_POST['docsub'])) {
        $doctor = !empty($_POST['doctor']) ? validateInput($_POST['doctor']) : null;
        $dpassword = !empty($_POST['dpassword']) ? password_hash(validateInput($_POST['dpassword']), PASSWORD_DEFAULT) : null;
        $demail = !empty($_POST['demail']) ? validateInput($_POST['demail']) : null;
        $spec = !empty($_POST['special']) ? validateInput($_POST['special']) : null;
        $docFees = !empty($_POST['docFees']) ? validateInput($_POST['docFees']) : null;
        $dgender = !empty($_POST['dgender']) ? validateInput($_POST['dgender']) : null;
        $docContact = !empty($_POST['docContact']) ? validateInput($_POST['docContact']) : null;
        $docAddress = !empty($_POST['docAddress']) ? validateInput($_POST['docAddress']) : null;

        if (!$doctor || !$dpassword || !$demail || !$spec || !$docFees || !$dgender || !$docContact || !$docAddress) {
            logError("Doctor registration error: Missing fields");
            echo "<script>alert('Please fill all fields.');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;
        }

        $stmt = $con->prepare("INSERT INTO doctb (username, password, email, spec, docFees, gender, docContact, docAddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $doctor, $dpassword, $demail, $spec, $docFees, $dgender, $docContact, $docAddress);

        if ($stmt->execute()) {
            echo "<script>alert('Doctor registration successful!');</script>";
            echo "<script>window.location.href = 'func.php';</script>";
        } else {
            logError("Doctor registration error: " . $stmt->error);
            echo "<script>alert('Error during registration. Please try again.');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        $stmt->close();
    } elseif (isset($_POST['adsub'])) {
        $username = !empty($_POST['username1']) ? validateInput($_POST['username1']) : null;
        $password = !empty($_POST['password2']) ? validateInput($_POST['password2']) : null;
        if (!$username || !$password) {
            echo "<script>alert('Please enter username and password.');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
            exit;
        }

        $stmt = $con->prepare("SELECT * FROM admintb WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin'] = $username;
                header("Location: admin-panel.php");
                exit;
            } else {
                echo "<script>alert('Invalid username or password');</script>";
                echo "<script>window.location.href = 'index.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid username or password');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        $stmt->close();
    } elseif (isset($_POST['update_data'])) {
        $contact = !empty($_POST['contact']) ? validateInput($_POST['contact']) : null;
        $status = !empty($_POST['status']) ? validateInput($_POST['status']) : null;

        if (!$contact || !$status) {
            echo "<script>alert('Please fill contact and status.');</script>";
            echo "<script>window.location.href = 'admin-panel.php';</script>";
            exit;
        }

        $stmt = $con->prepare("UPDATE appointmenttb SET payment = ? WHERE contact = ?");
        $stmt->bind_param("ss", $status, $contact);

        if ($stmt->execute()) {
            echo "<script>alert('Appointment payment status updated successfully!');</script>";
            echo "<script>window.location.href = 'admin-panel.php';</script>";
        } else {
            logError("Appointment update error: " . $stmt->error);
            echo "<script>alert('Error updating appointment status. Please try again.');</script>";
            echo "<script>window.location.href = 'admin-panel.php';</script>";
        }
        $stmt->close();
    }
}

function display_docs() {
    global $con;
    $stmt = $con->prepare("SELECT name, docFees FROM doctb");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['name']) . '" data-price="' . htmlspecialchars($row['docFees']) . '">' . htmlspecialchars($row['name']) . '</option>';
    }
    $stmt->close();
}

function display_admin_panel(){
	echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
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
                   <select name="doctor" class="form-control" >

                    <option value="" disabled selected>Select Doctor</option>
                    <option value="Dr. Punam Shaw">Dr. Punam Shaw</option>
                    <option value="Dr. Ashok Goyal">Dr. Ashok Goyal</option> 
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
        <div class="card">
          <div class="card-body">
            <form class="form-group" method="post" action="func.php">
              <input type="text" name="contact" class="form-control" placeholder="enter contact"><br>
              <select name="status" class="form-control">
               <option value="" disabled selected>Select Payment Status to update</option>
                <option value="paid">paid</option>
                <option value="pay later">pay later</option>
              </select><br><hr>
              <input type="submit" value="update" name="update_data" class="btn btn-primary">
            </form>
          </div>
        </div><br><br>
      </div>
      <div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">...</div>
      <div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
        <form class="form-group" method="post" action="func.php">
          <label>Doctors name: </label>
          <input type="text" name="name" placeholder="enter doctors name" class="form-control">
          <br>
          <input type="submit" name="doc_sub" value="Add Doctor" class="btn btn-primary">
        </form>
      </div>
       <div class="tab-pane fade" id="list-attend" role="tabpanel" aria-labelledby="list-attend-list">...</div>
    </div>
  </div>
</div>
   </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <!--Sweet alert js-->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.all.js"></script>
   <script type="text/javascript">
   $(document).ready(function(){
   	swal({
  title: "Welcome!",
  text: "Have a nice day!",
  imageUrl: "images/sweet.jpg",
  imageWidth: 400,
  imageHeight: 200,
  imageAlt: "Custom image",
  animation: false
})</script>
  </body>
</html>';
}
display_admin_panel();
?>