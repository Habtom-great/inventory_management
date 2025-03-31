<?php  
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Sorting logic
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Pagination settings
$limit = 10; // users per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch users with sorting and pagination
$query = "SELECT * FROM users WHERE role = 'user' ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching users: " . mysqli_error($conn));
}

// Count total users for pagination
$total_users_query = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'";
$total_users_result = mysqli_query($conn, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total'];
$total_pages = ceil($total_users / $limit);

// Check if there's a message and store it in a variable
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';

// Remove message after first display
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<?php
// Sorting logic
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'last_name';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Pagination settings
$limit = 10; // Vendors per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch vendors with sorting and pagination
$query = "SELECT * FROM vendors ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching vendors: " . mysqli_error($conn));
}

// Count total vendors for pagination
$total_vendors_query = "SELECT COUNT(*) AS total FROM vendors";
$total_vendors_result = mysqli_query($conn, $total_vendors_query);
$total_vendors = mysqli_fetch_assoc($total_vendors_result)['total'];
$total_pages = ceil($total_vendors / $limit);
?>

<!-- Show Success or Error Message -->
<?php if (isset($_SESSION['message'])): ?>
<div class="alert alert-<?php echo $_SESSION['message_type']; ?> text-center" role="alert">
 <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); // Remove message after displaying
                unset($_SESSION['message_type']);
            ?>
</div>
<?php endif; ?>


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
 display: flex;
 justify-content: flex-end;

 padding: 15px;
}

.navbar a {
 color: red;
 text-decoration: none;
 font-weight: bold;
 padding: 10px 15px;
 border-radius: 5px;
 margin-left: 10px;
}

.navbar a:hover {
 background-color: #375a8c;
}

.container {
 text-align: center;
}

.vendor-table {
 width: 80%;
 margin: auto;
 background-color: #fff;
 border-collapse: collapse;
 box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.1);
}

.vendor-table th,
.vendor-table td {
 text-align: center;
 padding: 12px;
 font-size: 14px;
}

/* Set Vendor ID and Vendor Name titles to white */
.vendor-table th:nth-child(1),
.vendor-table th:nth-child(2) {
 background-color: #4e73df;
 color: white;
}

.vendor-table th {
 background-color: #4e73df;
 color: white;
}

.pagination {
 margin-top: 20px;
 display: flex;
 justify-content: center;
}

.pagination a {
 color: #4e73df;
 padding: 8px 12px;
 border-radius: 5px;
 border: 1px solid #4e73df;
 margin: 0 5px;
}

.add-btn {
 background-color: rgb(17, 160, 124);
 color: white;
 place-items: right;
 padding: 6px 6px;
 border-radius: 5px;
}

.actions .edit-btn {
 background-color: #ffc107;
 color: white;
 padding: 6px 12px;
 border-radius: 5px;
}

.actions .view-btn {
 background-color: rgb(7, 106, 255);
 color: white;
 padding: 6px 6px;
 border-radius: 5px;
}

.actions .delete-btn {
 background-color: #dc3545;
 color: white;
 padding: 6px 12px;
 border-radius: 5px;
}

.footer {
 background-color: #343a40;
 color: white;
 text-align: center;
 padding: 15px;
 margin-top: 50px;
}
</style>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Manage Vendors</title>
 <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>

<body>
 <div class="header">Manage Vendors - Admin Panel</div>

 <!-- Display the delete message if available -->
 <?php if (!empty($message)): ?>
 <div class="alert alert-<?php echo $message_type; ?>   alert-dismissible fade show " role="alert"
  style="font-size: 16px; font-weight: bold; color:#dc3545">

  <?php echo $message; ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
 </div>
 <?php endif; ?>

 <!-- Navbar -->
 <div class="navbar">
  <button onclick="window.location.reload();" style="background: none; border: none; cursor: pointer;">
   <i class="fas fa-sync-alt" style="font-size: 24px; color: #000;"></i> Refresh
   <a href="logout.php" class="btn btn-logout">Logout</a>
  </button>
 </div>

 <head>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
 </head>

 <div class="container">
  <a href="add_vendor.php" class="add-btn">Add New Vendor</a>
  <h2 class="text-center">Vendor List</h2>
  <table class="vendor-table">
   <thead>
    <tr>
     <th><a
       href="?sort=vendor_id&order=<?php echo ($sort_column == 'vendor_id' && $sort_order == 'ASC') ? 'desc' : 'asc'; ?>"
       style="color: white; text-decoration: none;">Vendor ID</a></th>
     <th><a
       href="?sort=last_name&order=<?php echo ($sort_column == 'last_name' && $sort_order == 'ASC') ? 'desc' : 'asc'; ?>"
       style="color: white; text-decoration: none;">Last Name</a></th>
     <th><a
       href="?sort=middle_name&order=<?php echo ($sort_column == 'middle_name' && $sort_order == 'ASC') ? 'desc' : 'asc'; ?>"
       style="color: white; text-decoration: none;">Middle Name</a></th>
     <th><a
       href="?sort=first_name&order=<?php echo ($sort_column == 'first_name' && $sort_order == 'ASC') ? 'desc' : 'asc'; ?>"
       style="color: white; text-decoration: none;">First Name</a></th>

     <th>Email</th>
     <th>Phone</th>
     <th>Address</th>
     <th>Date</th>
     <th>Actions</th>
    </tr>
   </thead>
   <tbody>
    <?php while ($vendor = mysqli_fetch_assoc($result)) : ?>
    <tr>
     <td><?php echo $vendor['vendor_id']; ?></td>
     <td><?php echo $vendor['last_name']; ?></td>
     <td><?php echo $vendor['middle_name']; ?></td>
     <td><?php echo $vendor['first_name']; ?></td>
     <td><?php echo $vendor['email']; ?></td>
     <td><?php echo $vendor['telephone']; ?></td>
     <td><?php echo $vendor['address']; ?></td>

     <td><?php echo date("d-M-Y", strtotime($vendor['created_at'])); ?></td>
     <td class="actions">
      <a href="edit_vendor.php?vendor_id=<?php echo urlencode($user['vendor_id']); ?>" class="edit-btn">Edit</a>
      <a href="delete_vendor.php?vendor_id=<?php echo urlencode($vendor['vendor_id']); ?>" class="delete-btn"
       onclick="return confirm('Are you sure you want to delete this vendor?')">Delete</a>
      <a href="vendor_history.php?vendor_id=<?php echo urlencode($vendor['vendor_id']); ?>" class="view-btn">View
       History</a>
     </td>
    </tr>
    <?php endwhile; ?>
   </tbody>
  </table>

  <div class="pagination">
   <?php if ($page > 1): ?>
   <a
    href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>">Previous</a>
   <?php endif; ?>
   <?php if ($page < $total_pages): ?>
   <a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>">Next</a>
   <?php endif; ?>
  </div>
  <a href="admin_dashboard.php" class="back-btn">Back to Admin Dashboard</a>
 </div>
 <footer class="footer">&copy; <?php echo date("Y"); ?> Manage Vendors - All Rights Reserved.</footer>
</body>

</html>