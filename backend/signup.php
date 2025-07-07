<?php
session_start(); // ✅ Start the session to store login info

// DB Connection
$conn = mysqli_connect("localhost", "root", "", "student_portal");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ✅ Corrected button name: 'signup' not 'login'
if (isset($_POST['signup'])) {
    // Capture form inputs
    $fullname = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $course = $_POST['course'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate fields
    if (empty($fullname) || empty($email) || empty($phone) || empty($gender) || empty($course) || empty($password)) {
        die("Please fill all required fields.");
    }

    // Handle file upload
    $img_new = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $image_name = $_FILES['profile_image']['name'];
        $tmp_name = $_FILES['profile_image']['tmp_name'];

        if (!is_dir('uploads')) {
            mkdir('uploads');
        }

        $img_new = time() . '_' . $image_name;
        $upload_path = "uploads/" . $img_new;
        move_uploaded_file($tmp_name, $upload_path);
    }

    // Hash the password before saving
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO studentsinfo (fullname, email, phone, gender, course, profile_image, password, created_at)
            VALUES ('$fullname', '$email', '$phone', '$gender', '$course', '$img_new', '$hashed_password', NOW())";

    if (mysqli_query($conn, $sql)) {
        // ✅ Set session to use in dashboard
        $_SESSION['student_email'] = $email;

        echo "<script>
            alert('Student registered successfully!');
            window.location.href = 'dashboard.php'; // direct to dashboard
        </script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
