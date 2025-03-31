<?php
// Start session to access session variables if needed
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ABC_Company";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch invoices from the database with pagination
$limit = 10; // Number of invoices per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// SQL query to fetch invoices with pagination
$sql = "SELECT * FROM invoices LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get the total number of invoices for pagination
$total_result = $conn->query("SELECT COUNT(*) AS total FROM invoices");
$total_row = $total_result->fetch_assoc();
$total_invoices = $total_row['total'];
$total_pages = ceil($total_invoices / $limit);

?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Invoices - ABC Company</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
 <!-- Header Section -->
 <header class="bg-dark text-white text-center py-3">
  <h1>ABC Company Invoices</h1>
 </header>

 <div class="container mt-5">
  <h2 class="mb-4">Invoices</h2>

  <?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">Invoice created successfully!</div>
  <?php endif; ?>

  <table class="table table-bordered">
   <thead>
    <tr>
     <th>Invoice No</th>
     <th>Reference</th>
     <th>GL Account</th>
     <th>Amount Before VAT</th>
     <th>VAT (15%)</th>
     <th>Total After VAT</th>
     <th>Date</th>
     <th>Actions</th>
    </tr>
   </thead>
   <tbody>
    <?php if ($result->num_rows > 0): ?>
    <?php while ($invoice = $result->fetch_assoc()): ?>
    <?php
    // Calculations for VAT and total after VAT
    $amount_before_vat = $invoice['total_amount']; // Assuming total_amount is before VAT
    $vat = $amount_before_vat * 0.15; // 15% VAT
    $total_after_vat = $amount_before_vat + $vat;
    ?>
    <tr>
     <td><?= htmlspecialchars($invoice['invoice_no']) ?></td>
     <td><?= htmlspecialchars($invoice['reference']) ?></td>
     <td><?= htmlspecialchars($invoice['GL_account']) ?></td>
     <td>$<?= number_format($amount_before_vat, 2) ?></td>
     <td>$<?= number_format($vat, 2) ?></td>
     <td>$<?= number_format($total_after_vat, 2) ?></td>
     <td><?= date("d M Y", strtotime($invoice['created_at'])) ?></td>
     <td>
      <a href="view_invoice.php?id=<?= $invoice['invoice_id'] ?>" class="btn btn-info btn-sm">View</a>
      <a href="edit_invoice.php?id=<?= $invoice['invoice_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
      <a href="delete_invoice.php?id=<?= $invoice['invoice_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
     </td>
    </tr>
    <?php endwhile; ?>
    <?php else: ?>
    <tr>
     <td colspan="8" class="text-center">No invoices found.</td>
    </tr>
    <?php endif; ?>
   </tbody>
  </table>

  <!-- Pagination -->
  <nav aria-label="Page navigation">
   <ul class="pagination justify-content-center">
    <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
     <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
      <span aria-hidden="true">&laquo;</span>
     </a>
    </li>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
     <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>

    <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
     <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
      <span aria-hidden="true">&raquo;</span>
     </a>
    </li>
   </ul>
  </nav>

  <!-- Back to Create Invoice Button -->
  <div class="text-center mt-4">
   <a href="add_inventory.php" class="btn btn-primary">Back to add_inventory to Create Invoice</a>
  </div>
 </div>

 <!-- Footer Section -->
 <footer class="bg-dark text-white text-center py-3 mt-5">
  <p>&copy; 2025 ABC Company. All rights reserved.</p>
 </footer>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close the database connection at the end
$conn->close();
?>