<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = "127.0.0.1";
$username = "hmsuser";
$password = "varneeth";
$database = "myhmsdb";
session_start();

$con = mysqli_connect($host, $username, $password, $database, null, "/opt/lampp/var/mysql/mysql.sock");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['adsub'])) 	{
    $username = $_POST['username1'];
    $password = $_POST['password2'];

    $stmt = $con->prepare("SELECT * FROM admintb WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['username'] = $username;
        header("Location: admin-panel1.php");
        exit();
    } else {
        echo("<script>alert('Invalid Username or Password. Try Again!'); window.location.href = 'index.php';</script>");
    }
}

if (isset($_POST['update_data'])) {
    $contact = $_POST['contact'];
    $status = $_POST['status'];
    $stmt = $con->prepare("UPDATE appointmenttb SET payment=? WHERE contact=?");
    $stmt->bind_param("ss", $status, $contact);
    $result = $stmt->execute();

    if ($result) {
        header("Location: updated.php");
        exit();
    } else {
        echo("Error updating data: " . $con->error);
    }
}

function display_docs() {
    global $con;
    $query = "SELECT * FROM doctb";
    $result = mysqli_query($con, $query);

    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            $name = $row['name'];
            echo '<option value="' . htmlspecialchars($name) . '">' . htmlspecialchars($name) . '</option>';
        }
    } else {
        echo "Error fetching doctors: " . mysqli_error($con);
    }
}

if (isset($_POST['doc_sub'])) {
    $name = $_POST['name'];

    $stmt = $con->prepare("INSERT INTO doctb (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $result = $stmt->execute();

    if ($result) {
        header("Location: adddoc.php");
        exit();
    } else {
        echo("Error adding doctor: " . $con->error);
    }
}

mysqli_close($con);

?>
