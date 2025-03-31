<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abc_company";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$report_type = null;
$result = null;

// Report types for the dropdown menu
$report_types = [
    'branch' => 'Branch',
    'salesperson' => 'Salesperson',
    'date_range' => 'Date Range',
];

// Assuming the selected filter type and value are passed via GET or POST
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : "";
$report_value = isset($_GET['report_value']) ? $_GET['report_value'] : "All Items";

// Construct the title dynamically
if (!empty($report_type) && !empty($report_value)) {
    $title = "Inventory Report of " . ucfirst($report_type) . " " . htmlspecialchars($report_value);
} else {
    $title = "Inventory Report";
}

// Display the title
echo "<h2>$title</h2>";


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['report_type']) && $_POST['report_type'] === 'inventory') {
    $branch = $_POST['branch'] ?? '';
    $salesperson = $_POST['salesperson'] ?? '';
    $date_option = $_POST['date_option'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $specific_date = $_POST['specific_date'] ?? '';

    // Build the SQL query for the inventory report with filters
    $sql = "SELECT date, item_id, item_description, branch, salesperson, begin_qty, purchase_qty, sold_qty, 
            (begin_qty + purchase_qty - sold_qty) AS ending_qty, 
            (begin_qty * unit_cost) AS begin_cost, 
            (purchase_qty * unit_cost) AS purchase_cost, 
            (sold_qty * unit_cost) AS cost_of_sold, 
            ((begin_qty + purchase_qty - sold_qty) * unit_cost) AS ending_cost 
            FROM inventory WHERE 1";

    if (!empty($branch)) {
        $sql .= " AND branch = '" . $conn->real_escape_string($branch) . "'";
    }
    if (!empty($salesperson)) {
        $sql .= " AND salesperson = '" . $conn->real_escape_string($salesperson) . "'";
    }
    if ($date_option === 'range' && !empty($start_date) && !empty($end_date)) {
        $sql .= " AND date BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "'";
    } elseif ($date_option === 'specific' && !empty($specific_date)) {
        $sql .= " AND date = '" . $conn->real_escape_string($specific_date) . "'";
    }

    $sql .= " ORDER BY item_id ASC";
    $result = $conn->query($sql);
    if ($result === false) {
        die("SQL Error: " . $conn->error);
    }
    $report_type = 'inventory';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Inventory Report</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
 <style>
 body {
  background-color: #f8f9fa;
 }

 .header {
  background-color: #343a40;
  color: white;
  padding: 20px;
  text-align: center;
 }

 .container {
  margin-top: 30px;
 }

 .table-container {
  margin-top: 20px;
  background: white;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  border-radius: 8px;
 }
 </style>
</head>

<body>
 <div class="header">
  <h1>ABC Company</h1>
  <h2>Inventory Report</h2>
  <p>Filter inventory report by branch, salesperson, and date range.</p>
  <a href="manage_inventory.php" class="btn btn-light">Back to Inventory Dashboard</a>
 </div>

 <div class="container">
  <form method="POST" action="" class="mb-4">
   <div class="row">
    <div class="col-md-4">
     <label class="form-label">Branch</label>
     <select name="branch" class="form-select">
      <option value="">Select Branch</option>
      <option value="Addis Ababa">Addis Ababa</option>
      <option value="Hawassa">Hawassa</option>
      <option value="Mojjo">Mojjo</option>
     </select>
    </div>
    <div class="col-md-4">
     <label class="form-label">Salesperson</label>
     <select name="salesperson" class="form-select">
      <option value="">Select Salesperson</option>
      <option value="Asmamaw">Asmamaw</option>
      <option value="Tesfaye">Tesfaye</option>
      <option value="Kidst">Kidst</option>
     </select>
    </div>
    <div class="col-md-4">
     <label class="form-label">Date Option</label>
     <select name="date_option" class="form-select">
      <option value="">Select Date Option</option>
      <option value="range">Range</option>
      <option value="specific">Specific Date</option>
     </select>
    </div>
   </div>
   <div class="row mt-2">
    <div class="col-md-4">
     <label class="form-label">Start Date</label>
     <input type="date" name="start_date" class="form-control">
    </div>
    <div class="col-md-4">
     <label class="form-label">End Date</label>
     <input type="date" name="end_date" class="form-control">
    </div>
    <div class="col-md-4">
     <label class="form-label">Specific Date</label>
     <input type="date" name="specific_date" class="form-control">
    </div>
   </div>
   <div class="row mt-2">
    <div class="col-md-12">
     <button type="submit" name="report_type" value="inventory" class="btn btn-primary w-100 mt-3">Generate
      Report</button>
    </div>
   </div>
  </form>

  <?php if ($report_type === 'inventory' && $result && $result->num_rows > 0): ?>
  <div class="table-container">
   <h3>Inventory Report</h3>
   <table class="table table-striped table-bordered">
    <thead class="table-dark">
     <tr>
      <th>Date</th>
      <th>Item ID</th>
      <th>Item Description</th>
      <th>Branch</th>
      <th>Salesperson</th>
      <th>Beginning Quantity</th>
      <th>Purchased Quantity</th>
      <th>Sold Quantity</th>
      <th>Ending Quantity</th>
      <th>Beginning Cost</th>
      <th>Purchase Cost</th>
      <th>Cost of Quantity Sold</th>
      <th>Ending Cost</th>
     </tr>
    </thead>
    <tbody>
     <?php while ($row = $result->fetch_assoc()): ?>
     <tr>
      <?php foreach ($row as $value): ?>
      <td><?= htmlspecialchars($value) ?></td>
      <?php endforeach; ?>
     </tr>
     <?php endwhile; ?>
    </tbody>
   </table>
  </div>
  <?php endif; ?>
 </div>
</body>

</html>

kkkkkkkkkkkkkkk
<?php
// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "abc_company";
$conn = new mysqli($servername, $username, $password, $dbname);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);




// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter values
$branch = $_POST['branch'] ?? '';
$salesperson = $_POST['salesperson'] ?? '';
$date_option = $_POST['date_option'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$specific_date = $_POST['specific_date'] ?? '';

// Build query dynamically
$query = "SELECT * FROM inventory WHERE 1=1";
$params = [];
$types = "";
  // Build the SQL query for the inventory report with filters
$sql = "SELECT date, item_id, item_description, branch, salesperson, begin_qty, purchase_qty, sold_qty, 
(begin_qty + purchase_qty - sold_qty) AS ending_qty, 
(begin_qty * unit_cost) AS begin_cost, 
(purchase_qty * unit_cost) AS purchase_cost, 
(sold_qty * unit_cost) AS cost_of_sold, 
((begin_qty + purchase_qty - sold_qty) * unit_cost) AS ending_cost 
FROM inventory WHERE 1";
// Apply filters
if (!empty($branch)) {
    $query .= " AND branch = ?";
    $params[] = $branch;
    $types .= "s";
}

if (!empty($salesperson)) {
    $query .= " AND salesperson = ?";
    $params[] = $salesperson;
    $types .= "s";
}

if ($date_option === "specific" && !empty($specific_date)) {
    $query .= " AND date = ?";
    $params[] = $specific_date;
    $types .= "s";
} elseif ($date_option === "range" && !empty($start_date) && !empty($end_date)) {
    $query .= " AND date BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= "ss";
}

// Prepare and execute query
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Generate Report Title
$title = "Inventory Report";
if (!empty($branch)) {
    $title .= " for Branch: " . htmlspecialchars($branch);
}
if (!empty($salesperson)) {
    $title .= " | Salesperson: " . htmlspecialchars($salesperson);
}
if ($date_option === "specific" && !empty($specific_date)) {
    $title .= " | Date: " . htmlspecialchars($specific_date);
} elseif ($date_option === "range" && !empty($start_date) && !empty($end_date)) {
    $title .= " | From: " . htmlspecialchars($start_date) . " to " . htmlspecialchars($end_date);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?php echo $title; ?></title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
 <div class="container mt-4">
  <h2 class="text-center"><?php echo $title; ?></h2>

  <!-- Filter Form -->
  <form method="POST" class="mb-3">
   <div class="row">
    <div class="col-md-3">
     <label for="branch">Branch:</label>
     <input type="text" name="branch" id="branch" class="form-control" value="<?php echo htmlspecialchars($branch); ?>">
    </div>

    <div class="col-md-3">
     <label for="salesperson">Salesperson:</label>
     <input type="text" name="salesperson" id="salesperson" class="form-control"
      value="<?php echo htmlspecialchars($salesperson); ?>">
    </div>

    <div class="col-md-3">
     <label for="date_option">Date Filter:</label>
     <select name="date_option" id="date_option" class="form-select">
      <option value="">Select</option>
      <option value="specific" <?php if ($date_option == "specific") echo "selected"; ?>>Specific Date</option>
      <option value="range" <?php if ($date_option == "range") echo "selected"; ?>>Date Range</option>
     </select>
    </div>
   </div>

   <div class="row mt-2">
    <div class="col-md-3">
     <label for="specific_date">Specific Date:</label>
     <input type="date" name="specific_date" id="specific_date" class="form-control"
      value="<?php echo htmlspecialchars($specific_date); ?>">
    </div>

    <div class="col-md-3">
     <label for="start_date">Start Date:</label>
     <input type="date" name="start_date" id="start_date" class="form-control"
      value="<?php echo htmlspecialchars($start_date); ?>">
    </div>

    <div class="col-md-3">
     <label for="end_date">End Date:</label>
     <input type="date" name="end_date" id="end_date" class="form-control"
      value="<?php echo htmlspecialchars($end_date); ?>">
    </div>
   </div>

   <button type="submit" class="btn btn-primary mt-3">Filter</button>
  </form>

  <!-- Report Table -->
  <table class="table table-bordered">
   <thead class="table-dark">
    <tr>
     <th>Date</th>
     <th>Item ID</th>
     <th>Item Description</th>
     <th>Branch</th>
     <th>Salesperson</th>
     <th>Beginning Quantity</th>
     <th>Purchased Quantity</th>
     <th>Sold Quantity</th>
     <th>Ending Quantity</th>
     <th>Beginning Cost</th>
     <th>Purchase Cost</th>
     <th>Cost of Quantity Sold</th>
     <th>Ending Cost</th>
    </tr>
   </thead>
   <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
     <td><?php echo htmlspecialchars($row['date']); ?></td>
     <td><?php echo htmlspecialchars($row['item_id']); ?></td>
     <td><?php echo htmlspecialchars($row['item_description']); ?></td>
     <td><?php echo htmlspecialchars($row['branch']); ?></td>
     <td><?php echo htmlspecialchars($row['salesperson']); ?></td>
     <td><?php echo htmlspecialchars($row['beginning_quantity']); ?></td>
     <td><?php echo htmlspecialchars($row['purchased_quantity']); ?></td>
     <td><?php echo htmlspecialchars($row['sold_quantity']); ?></td>
     <td><?php echo htmlspecialchars($row['ending_quantity']); ?></td>
     <td><?php echo htmlspecialchars($row['beginning_cost']); ?></td>
     <td><?php echo htmlspecialchars($row['purchase_cost']); ?></td>
     <td><?php echo htmlspecialchars($row['cost_of_quantity_sold']); ?></td>
     <td><?php echo htmlspecialchars($row['ending_cost']); ?></td>
    </tr>
    <?php } ?>
   </tbody>
  </table>
 </div>

</body>

</html>

<?php
// Close connection
$stmt->close();
$conn->close();
?>