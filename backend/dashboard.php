<?php
session_start();
if (!isset($_SESSION['student_email'])) {
    header("Location: login.html");
    exit();
}

// DB Connection
$conn = mysqli_connect("localhost", "root", "", "student_portal");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo"connect suessfully";

$email = $_SESSION['student_email'];

// Fetch full data
$sql = "SELECT * FROM studentsinfo WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $student = mysqli_fetch_assoc($result);
} else {
    echo "Student data not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="../frontend/css/dashboard.css">
</head>
<body>

  <main>
    <div class="navbar">
      <div class="buttons">
        <!-- Change-btn -->
        <a href="change_details.php" class="btn">Change Details</a>
        <!-- Delete-profile-btn -->
          <form action="delete.php" method="POST" onsubmit="return confirm('Are You Sure That You Want To Delete Your Profile Permanently')">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
            <button type="submit" name="delete" class="btn" >Delete Your Profile</button>
          </form>
          <!-- Login-btn -->
          <a href="../frontend/login.html" onsubmit="return confirm('Are You want to LogOut')" class="btn">LogOut</a>
      </div>
  </div>

  <div class="dashboard">
    
    <h1>Welcome, <?php echo htmlspecialchars($student['fullname']); ?>!</h1>

    <div class="info">
      <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
      <p><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender']); ?></p>
      <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>
      <p><strong>Created_at:</strong> <?php echo htmlspecialchars($student['created_at']); ?></p>
      <img src="uploads/<?php echo htmlspecialchars($student['Profile_image']); ?>" alt="Profile Picture" width="100" height="100">

    </div>

    
  </div>
  </main>
</body>
</html>
