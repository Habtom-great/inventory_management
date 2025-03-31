<?php
include 'db_connection.php';

// Fetch all staff records
$sql = "SELECT * FROM staff ORDER BY hire_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Staff History</title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
 <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
 <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

 <style>
 body {
  background-color: #f8f9fa;
  font-size: 14px;
 }

 .container {
  margin-top: 20px;
 }

 .table thead {
  background-color: #007bff;
  color: white;
 }

 .profile-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
 }

 .header {
  background: linear-gradient(90deg, #007bff, #0056b3);
  color: white;
  padding: 10px;
  text-align: center;
  font-size: 16px;
  font-weight: bold;
 }

 .footer {
  background: #343a40;
  color: white;
  text-align: center;
  padding: 8px;
  margin-top: 20px;
  font-size: 15px;
 }

 .buttons-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
 }

 .buttons-container .btn {
  font-size: 9px;
  padding: 5px 10px;
 }

 .btn-logout {
  margin-left: 10px;
 }

 /* Style for search input */
 .search-box {
  margin-bottom: 15px;
 }

 .search-box input {
  width: 100%;
  max-width: 400px;
  padding: 5px;
  font-size: 12px;
  border: 1px solid #ccc;
  border-radius: 5px;
 }
 </style>
</head>

<body>

 <!-- Header -->
 <div class="header">Staff History - Admin Panel</div>

 <div class="container">
  <div class="d-flex justify-content-between align-items-center mb-2">
   <h4 class="text-center">Staff History</h4>
   <div class="buttons-container">
    <button id="exportExcel" class="btn btn-success btn-sm">Export to Excel</button>
    <button id="exportPDF" class="btn btn-danger btn-sm">Export to PDF</button>
    <button id="printTable" class="btn btn-primary btn-sm">Print</button>
    <a href="logout.php" class="btn btn-warning btn-sm btn-logout">Logout</a>
   </div>
  </div>

  <?php if ($result->num_rows > 0): ?>
  <div class="table-responsive">
   <table id="staffTable" class="table table-striped table-hover table-bordered">
    <thead class="text-center">
     <tr>
      <th>Profile</th>
      <th>Staff ID</th>
      <th>Full Name</th>
      <th>Department</th>
      <th>Position</th>
      <th>Salary</th>
      <th>Email</th>
      <th>Telephone</th>
      <th>Hire Date</th>
      <th>Termination Date</th>
      <th>Experience</th>
      <th>Skills</th>
     </tr>
    </thead>
    <tbody>
     <?php while ($row = $result->fetch_assoc()): ?>
     <tr>
      <td class="text-center">
       <img src="<?= !empty($row['profile_image']) ? 'uploads/'.$row['profile_image'] : 'uploads/default.png'; ?>"
        class="profile-img">
      </td>
      <td><?= htmlspecialchars($row['staff_id']); ?></td>
      <td><?= htmlspecialchars($row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name']); ?></td>
      <td><?= htmlspecialchars($row['department']); ?></td>
      <td><?= htmlspecialchars($row['position']); ?></td>
      <td><?= htmlspecialchars($row['salary']); ?></td>
      <td><?= htmlspecialchars($row['email']); ?></td>
      <td><?= htmlspecialchars($row['telephone']); ?></td>
      <td><?= htmlspecialchars($row['hire_date']); ?></td>
      <td><?= htmlspecialchars($row['termination_date']); ?></td>
      <td><?= htmlspecialchars($row['experience']); ?> years</td>
      <td><?= htmlspecialchars($row['skills']); ?></td>
     </tr>
     <?php endwhile; ?>
    </tbody>
   </table>
  </div>
  <?php else: ?>
  <p class="alert alert-warning text-center">No staff history available.</p>
  <?php endif; ?>
 </div>

 <div class="container text-center mt-3">
  <a class="btn btn-dark btn-sm" href="admin_dashboard.php">Back to Admin Dashboard</a>
 </div>

 <!-- Footer -->
 <div class="footer">© 2025 ABC Company | All Rights Reserved</div>

 <script>
 $(document).ready(function() {
  var table = $('#staffTable').DataTable({


  });

  // Search functionality
  $('#searchInput').on('keyup', function() {
   table.search(this.value).draw();
  });

  // Handle button clicks manually
  $('#exportExcel').click(function() {
   $('.buttons-excel').click();
  });

  $('#exportPDF').click(function() {
   $('.buttons-pdf').click();
  });

  $('#printTable').click(function() {
   $('.buttons-print').click();
  });
 });
 </script>

</body>

</html>

<?php
$conn->close();
?>