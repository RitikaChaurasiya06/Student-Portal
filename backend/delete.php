<?php
// connect to database
$conn = new mysqli("localhost", "root","","student_portal");

// check connection

if($conn -> connect_error){
    die("Connection failed:" . $conn->connect_error);
}

// delete logic
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])){
    $id = $_POST['student_id'];

    // safety tip: Use prepared statement
    $stmt = $conn->prepare("DELETE FROM studentsinfo WHERE id = ?");
    $stmt->bind_param("i", $id);
        

    if($stmt->execute()){
        echo"<script>
            alert('Student record deleted successfully');
            window.location.href = '../frontend/login.html';
        </script>";
    }
    else{
        echo"Error deleting record:" .$stmt->error;
    }
    $stmt->close();
}

$conn->close();


?>