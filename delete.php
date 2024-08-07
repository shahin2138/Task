<?php 

$id = $_GET["id"];

$servername ="localhost";
$username = "root";
$password = "";
$dbname = "management";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if (!$conn) {
    echo "database not connected". mysqli_connect_error();
}

$sql = "DELETE FROM `users` WHERE id = $id";


if (mysqli_query($conn,$sql)) {
    header("Location:index.php");
} else {
    echo "Something went wrong";
}

mysqli_close($conn);

?>