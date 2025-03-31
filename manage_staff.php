<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all staff from the database
$query = "SELECT * FROM staff";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching staff: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Manage Staff</title>
 <link rel="stylesheet" href="assets/css/bootstrap.min.css">
 <style>
 body {
  background-color: #f4f7fc;
  font-family: 'Arial', sans-serif;
 }

 .header {
  background-color: #4e73df;
  color: white;
  text-align: center;
  padding: 20px;
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 30px;
 }

 .navbar {
  margin-bottom: 30px;
 }

 .staff-container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  min-height: 60vh;
 }

 .staff-table {
  width: 80%;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
 }

 .staff-table thead {
  background-color: #4e73df;
  color: white;
 }

 .staff-table th,
 .staff-table td {
  text-align: center;
  padding: 12px 15px;
 }

 .staff-table th {
  font-size: 16px;
 }

 .staff-table td {
  font-size: 14px;
 }

 .btn-custom {
  background-color: #4e73df;
  color: white;
  padding: 10px 15px;
  border-radius: 8px;
  font-weight: bold;
 }

 .btn-custom:hover {
  background-color: #375a8c;
 }

 .footer {
  background-color: #343a40;
  color: white;
  text-align: center;
  padding: 15px;
  margin-top: 50px;
 }

 .actions a {
  text-decoration: none;
  margin: 0 5px;
 }

 .actions .edit-btn {
  background-color: #ffc107;
  color: white;
 }

 .actions .delete-btn {
  background-color: #dc3545;
  color: white;
 }

 .actions .edit-btn:hover {
  background-color: #e0a800;
 }

 .actions .delete-btn:hover {
  background-color: #c82333;
 }

 .actions .staff-btn:hover {
  background-color: rgb(8, 155, 111);
 }

 .actions .staff-btn:hover {
  background-color: rgb(172, 96, 235);
 }
 </style>
</head>

<body>

 <!-- Header -->
 <div class="header">
  Manage Staff - Admin Panel
 </div>

 <!-- Navbar -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="ml-auto">
   <a href="view_all_staff_history.php" class="btn btn-light me-2">View_All_Staff_History</a>
   <a href="add_staff.php" class="btn btn-light me-2">Add Staff</a>
   <a href="payroll.php" class="btn btn-light me-2">Payroll</a>
   <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>
 </nav>

 <!-- Staff Table Section -->
 <div class="container staff-container">
  <h2 class="text-center">Staff Members</h2>

  <div class="staff-table">
   <table class="table table-striped">
    <thead>
     <tr>
      <th>Staff ID</th>


      <th>Last Name</th>
      <th>Middle Name</th>
      <th>First Name</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Profile Image</th>
      <th>Actions</th>
     </tr>
    </thead>
    <tbody>
     <?php while ($staff = mysqli_fetch_assoc($result)) : ?>
     <tr>
      <td><?php echo $staff['staff_id']; ?></td>
      <td><?php echo $staff['last_name']; ?></td>

      <td><?php echo $staff['middle_name']; ?></td>
      <td><?php echo $staff['first_name']; ?></td>
      <td><?php echo $staff['email']; ?></td>
      <td><?php echo $staff['telephone']; ?></td>
      <td><img src="<?php echo $staff['profile_image']; ?>" alt="Profile" width="50" height="50"></td>
      <td class="actions">
       <a href="edit_staff.php?staff_id=<?php echo urlencode($staff['staff_id']); ?>"
        class="btn btn-custom edit-btn">Edit</a>
       <a href="delete_staff.php?staff_id=<?php echo urlencode($staff['staff_id']); ?>"
        class="btn btn-custom delete-btn"
        onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
       <a href="staff_history.php?staff_id=<?php echo urlencode($staff['staff_id']); ?>"
        class="btn btn-custom staff-history-btn">View History</a>
      </td>
     </tr>
     <?php endwhile; ?>
    </tbody>
   </table>
  </div>
 </div>

 <div class="container">
  <a class="navbar-brand fw-bold" href="admin_dashboard.php">Back to Admin Dashboard</a>

  <!-- Footer -->
  <footer class="footer">
   &copy; <?php echo date("Y"); ?> Manage Staff - All Rights Reserved.
  </footer>
 </div>

 <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>