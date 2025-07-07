<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "student_portal");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please fill all fields'); window.location.href='../frontend/login.html';</script>";
        exit();
    }

    $query = "SELECT * FROM studentsinfo WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['student_email'] = $user['email'];
            $_SESSION['student_name'] = $user['fullname'];

            echo "<script>
                alert('Login successful!');
                window.location.href = '../backend/dashboard.php';
            </script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password'); window.location.href='../frontend/login.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found'); window.location.href='../frontend/login.html';</script>";
    }
}
?>
