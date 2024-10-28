<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$host = "127.0.0.1";
$username = "hmsuser";
$password = "varneeth";
$database = "myhmsdb";

$con = mysqli_connect($host, $username, $password, $database);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['entry_submit'])) {
        $fname = !empty($_POST['fname']) ? validateInput($_POST['fname']) : null;
        $lname = !empty($_POST['lname']) ? validateInput($_POST['lname']) : null;
        $email = !empty($_POST['email']) ? validateInput($_POST['email']) : null;
        $contact = !empty($_POST['contact']) ? validateInput($_POST['contact']) : null;
        $doctor = !empty($_POST['doctor']) ? validateInput($_POST['doctor']) : null;
        if (!$fname || !$lname || !$email || !$contact || !$doctor) {
            echo "<script>alert('Please fill all fields.');</script>";
            echo "<script>window.location.href = 'admin-panel.php';</script>";
            exit;
        }
        $stmt = $con->prepare("INSERT INTO appointmenttb (fname, lname, email, contact, doctor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fname, $lname, $email, $contact, $doctor);

        if ($stmt->execute()) {
            echo "<script>alert('Appointment created successfully!');</script>";
            echo "<script>window.location.href = 'admin-panel.php';</script>";
        } else {
            echo "<script>alert('Error creating appointment. Please try again.');</script>";
            echo "<script>window.location.href = 'admin-panel.php';</script>";
        }
        $stmt->close();
    }
}
?>
