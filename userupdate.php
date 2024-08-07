<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $gender = $_POST["gender"];
    $experience_ids = $_POST['experience_id'];
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

    // Update user 
    $sql = "UPDATE users SET name=?, email=?, mobile=?, gender=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $mobile, $gender, $id);

    if ($stmt->execute()) {
        // Delete experiences that are not in the form
        $sql_del = "DELETE FROM experiences WHERE user_id = ? AND id NOT IN (" . implode(',', array_map('intval', $experience_ids)) . ")";
        $stmt_del = $conn->prepare($sql_del);
        $stmt_del->bind_param("i", $id);
        $stmt_del->execute();
        $stmt_del->close();

        // Update existing experiences
        $sql_exp = "UPDATE experiences SET company=?, years=?, months=? WHERE id=?";
        $stmt_exp = $conn->prepare($sql_exp);
        $stmt_exp->bind_param("siii", $company, $year, $month, $experience_id);

        for ($i = 0; $i < count($experience_ids); $i++) {
            $experience_id = $experience_ids[$i];
            $company = $companies[$i];
            $year = $years[$i];
            $month = $months[$i];

            if (!$stmt_exp->execute()) {
                echo "Error updating experience: " . $stmt_exp->error;
            }
        }

        // Insert new experiences
        $sql_ins = "INSERT INTO experiences (user_id, company, years, months) VALUES (?, ?, ?, ?)";
        $stmt_ins = $conn->prepare($sql_ins);
        $stmt_ins->bind_param("isis", $id, $company, $year, $month);

        for ($i = 0; $i < count($companies); $i++) {
            
            if (empty($experience_ids[$i])) {
                $company = $companies[$i];
                $year = $years[$i];
                $month = $months[$i];

                if (!$stmt_ins->execute()) {
                    echo "Error inserting experience: " . $stmt_ins->error;
                }
            }
        }

        header("Location:index.php");
    } else {
        echo "Error updating user: " . $stmt->error;
    }

  
    $stmt->close();
    $stmt_exp->close();
    $stmt_ins->close();
    $conn->close();
}
?>
