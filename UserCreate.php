<?php 

$name = $_POST["name"];
$email = $_POST["email"];
$mobile = $_POST["mobile"];
$gender = $_POST["gender"];
$companies = $_POST['company'];
$years = $_POST['years'];
$months = $_POST['months'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "management";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("INSERT INTO `users` (`name`, `email`, `mobile`, `gender`) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $mobile, $gender);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id; 
    
    
    $stmt_exp = $conn->prepare("INSERT INTO experiences (user_id, company, years, months) VALUES (?, ?, ?, ?)");
    $stmt_exp->bind_param("isis", $user_id, $company, $year, $month);
    
    // Insert experience data
    for ($i = 0; $i < count($companies); $i++) {
       
        $company = $companies[$i];
        $year = $years[$i];
        $month = $months[$i];
        
        if (!$stmt_exp->execute()) {
            echo "Error inserting experience: " . $stmt_exp->error;
        }
    }
    
    header("Location:index.php");
} else {
    echo "Error inserting user: " . $stmt->error;
}


$stmt->close();
$stmt_exp->close();
$conn->close();

?>
