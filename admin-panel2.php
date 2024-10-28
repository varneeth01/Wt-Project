<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = 'localhost';
$username = 'root';
$password = 'varneeth';
$database = 'myhmsdb';
$socket = '/run/mysqld/mysqld.sock';

$con = mysqli_connect($host, $username, $password, $database, null, $socket);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

include('newfunc.php');
if (isset($_POST['docsub'])) {
    $doctor = $_POST['doctor'];
    $dpassword = $_POST['dpassword'];
    $demail = $_POST['demail'];
    $spec = $_POST['special'];
    $docFees = $_POST['docFees'];
    $dgender = $_POST['dgender'];
    $docAddress = $_POST['docAddress'];
    $docContact = $_POST['docContact'];
    $lat = isset($_POST['lat']) && !empty($_POST['lat']) ? $_POST['lat'] : 0;
    $lng = isset($_POST['lng']) && !empty($_POST['lng']) ? $_POST['lng'] : 0;
    $stmt = $con->prepare("INSERT INTO doctb(username, password, email, spec, docFees, gender, docContact, docAddress, lat, lng) 
                           VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdsdsdd", $doctor, $dpassword, $demail, $spec, $docFees, $dgender, $docContact, $docAddress, $lat, $lng);
    if (!$stmt->execute()) {
        die('Query Failed: ' . $stmt->error);
    } else {
        echo "<script>alert('Doctor added successfully!');</script>";
        echo '<script>window.location.href = "/index.php";</script>';
    }
    $stmt->close();
}
mysqli_close($con);
?>
