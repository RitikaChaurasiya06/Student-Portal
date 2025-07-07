<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "student_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$emaill = $_SESSION['student_email'] ?? '';
if (empty($emaill)) {
    die("Session expired. Please login again.");
}

// Fetch student data
$sql = "SELECT * FROM studentsinfo WHERE email = '$emaill'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    echo "Student not found!";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $course = $_POST['course'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $profile_image = $student['profile_image']; // Default: keep old image

    // Only update image if user checked the checkbox and uploaded a file
    if (isset($_POST['update_image']) && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['profile_image']['tmp_name'];
        $img_name = uniqid() . '_' . $_FILES['profile_image']['name'];
        $upload_path = 'uploads/' . $img_name;
        move_uploaded_file($tmp_name, $upload_path);
        $profile_image = $img_name;
    }

    // Prepare update
    $stmt = $conn->prepare("UPDATE studentsinfo SET fullname = ?, email = ?, phone = ?, gender = ?, course = ?, profile_image = ?, password = ? WHERE email = ?");
    $stmt->bind_param("ssssssss", $fullname, $email, $phone, $gender, $course, $profile_image, $hashed_password, $emaill);

    if ($stmt->execute()) {
        $_SESSION['student_email'] = $email;
        echo "<script>
                alert('Your details were updated successfully!');
                window.location.href = 'dashboard.php';
              </script>";
        exit();
    } else {
        echo "Error updating: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Portal - Update Details</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="form-container">
        <h2>Update Your Details Here</h2>

        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Profile Image -->
            <label>Profile Image</label><br>
            <img src="uploads/<?php echo htmlspecialchars($student['Profile_image']); ?>" height="100" width="100" style="border-radius: 50%;"><br>
            
            <input type="file" name="profile_image" accept="image/*"><br>
            <label>
              <input type="checkbox" name="update_image" value="1">
              Check this box to update your profile image
            </label><br><br>

            <!-- Other fields -->
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required><br>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required><br>

            <label>Phone No.</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required><br>

            <label>Gender</label>
            <input type="text" name="gender" value="<?php echo htmlspecialchars($student['gender']); ?>" required><br>

            <label>Course</label>
            <input type="text" name="course" value="<?php echo htmlspecialchars($student['course']); ?>" required><br>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your new password" required><br><br>

            <button type="submit" onclick="return confirm('Are you sure to update your details?')">Update Your Info</button>
        </form>
    </div>
</body>
</html>
