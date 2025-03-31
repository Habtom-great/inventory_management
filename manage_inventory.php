<?php
session_start();

// Simulated login
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = 'Admin'; // Simulated user login
}

// Simulated data for the graph (replace with database data)
$data = [
    "labels" => ["January", "February", "March", "April", "May", "June"],
    "values" => [500, 700, 1000, 1200, 1500, 1800],
];

// Report types for the dropdown menu
$report_types = [
    'inventory_reports/generate_report-1.php' => 'Generate Report-1',
    'inventory_reports/generate_report-2.php' => 'Generate Report-2',
    'inventory_reports/inventory_summary.php' => 'Inventory Summary-1',
    'inventory_reports/inventory_summary-2.php' => 'Inventory Summary-2',
    'inventory_reports/daily_inventory.php' => 'Daily Inventory Reports',
    'inventory_reports/other_report.php' => 'other Reports',
    'inventory_reports/low_stock.php' => 'Low Stock Items',
    'inventory_reports/out_of_stock.php' => 'Out of Stock Report',
    'inventory_reports/filtered_inventory.php' => 'Filtered Inventory Report',
    'inventory_reports/sales_report.php' => 'Sales Report',  
    'special inventory report.php' => 'special Report'
      
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Inventory Management Dashboard</title>
 <link rel="stylesheet" href="styles.css">
 <script src="js/chart.js" defer></script> <!-- Local Chart.js -->
 <script src="scripts.js" defer></script> <!-- Custom JavaScript -->
 <style>
 /* General Styling */
 body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background-color: #f9f9f9;
 }

 header {
  background-color: #4CAF50;
  color: white;
  padding: 15px;
  text-align: center;
  font-size: 20px;
 }

 nav {
  background-color: #333;
  display: flex;
  justify-content: space-around;
  padding: 10px;
 }

 nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
 }

 nav ul li {
  position: relative;
  padding: 10px;
 }

 nav ul li a {
  color: white;
  text-decoration: none;
  padding: 10px;
  display: block;
 }

 /* Dropdown menu styling */
 .dropdown {
  position: relative;
  display: inline-block;
 }

 .dropdown-content {
  display: none;
  position: absolute;
  background-color: #333;
  min-width: 200px;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
 }

 .dropdown-content a {
  color: white;
  padding: 10px;
  text-decoration: none;
  display: block;
 }

 .dropdown:hover .dropdown-content {
  display: block;
 }

 /* Main content styling */
 main {
  padding: 20px;
 }

 .summary-cards {
  display: flex;
  gap: 15px;
  justify-content: space-around;
 }

 .card {
  background-color: #fff;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 180px;
 }

 .graph-report {
  text-align: center;
  margin-top: 20px;
 }

 footer {
  background-color: #4CAF50;
  color: white;
  text-align: center;
  padding: 10px;
  position: fixed;
  bottom: 0;
  width: 100%;
 }
 </style>
</head>

<body>

 <header>
  <h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>
 </header>

 <nav>
  <ul>
   <li><a href="add_inventory.php">Add Inventory</a></li>
   <li><a href="edit_inventory.php">Edit Inventory</a></li>
   <li><a href="delete_inventory.php">Delete Inventory</a></li>
   <li><a href="adjust_inventory.php">Adjust Inventory</a></li>
   <li class="dropdown">
    <a href="#">Reports ▼</a>
    <div class="dropdown-content">
     <?php foreach ($report_types as $file => $name) : ?>
     <a href="<?php echo $file; ?>"><?php echo $name; ?></a>
     <?php endforeach; ?>
    </div>
   </li>
   <li><a href="salesperson_performance.php">Performance</a></li>
  </ul>
 </nav>

 <main>
  <section class="summary">
   <h2>Dashboard Summary</h2>
   <div class="summary-cards">
    <div class="card">
     <h3>Total Inventory Value</h3>
     <p>$12,500</p>
    </div>
    <div class="card">
     <h3>Total Items</h3>
     <p>650</p>
    </div>
    <div class="card">
     <h3>Active Branches</h3>
     <p>7</p>
    </div>
   </div>
  </section>

  <section class="graph-report">
   <h2>Monthly Inventory Growth</h2>
   <canvas id="inventoryChart"></canvas>
  </section>

  <section class="updates">
   <h2>Recent Updates</h2>
   <ul>
    <li>📦 Added: ???????100 units of Product Z</li>
    <li>🔄 Adjusted: ?????5 units of Product X removed</li>
    <li>✨ Updated pricing for seasonal items</li>
   </ul>
  </section>
 </main>

 <footer>
  <p>&copy; 2025 Inventory Manager Pro | Powered by Local Technology</p>
 </footer>

 <script>
 // Chart.js configuration
 const graphData = <?php echo json_encode($data); ?>;

 document.addEventListener("DOMContentLoaded", () => {
  const ctx = document.getElementById("inventoryChart").getContext("2d");

  new Chart(ctx, {
   type: "line",
   data: {
    labels: graphData.labels,
    datasets: [{
     label: "Inventory Value ($)",
     data: graphData.values,
     borderColor: "#2d89ef",
     backgroundColor: "rgba(45, 137, 239, 0.1)",
     borderWidth: 2,
     tension: 0.4,
    }]
   },
   options: {
    responsive: true,
    plugins: {
     legend: {
      display: true,
      position: "top"
     }
    },
    scales: {
     x: {
      title: {
       display: true,
       text: "Months"
      }
     },
     y: {
      title: {
       display: true,
       text: "Inventory Value ($)"
      }
     }
    }
   }
  });
 });
 </script>

</body>

</html>