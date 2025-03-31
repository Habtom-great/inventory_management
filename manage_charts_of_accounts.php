<?php
// Include necessary files
include('header.php');
include('db_connection.php');

// Sorting Logic: default sort by account_id
$order_by = 'account_id'; // Default sorting column
$order_dir = 'ASC'; // Default sorting direction

// Check if there's a sorting request
if (isset($_GET['sort_by'])) {
    $order_by = mysqli_real_escape_string($conn, $_GET['sort_by']);
}
if (isset($_GET['order_dir'])) {
    $order_dir = mysqli_real_escape_string($conn, $_GET['order_dir']);
}

// SQL query to group and order the accounts based on account classification and sorting option
$query = "SELECT * FROM charts_of_accounts 
          ORDER BY 
            CASE 
                WHEN account_classification = 'Assets' THEN 1
                WHEN account_classification = 'Liabilities' THEN 2
                WHEN account_classification = 'Capital' THEN 3
                WHEN account_classification = 'Purchases' THEN 4
                WHEN account_classification = 'COGS' THEN 5
                WHEN account_classification = 'Revenue' THEN 6
                WHEN account_classification = 'Expenses' THEN 7
            END,
            $order_by $order_dir"; // Apply sorting by selected column and direction

$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn)); // Display error if the query failed
}
?>

<!-- Sorting options -->
<form method="get" action="manage_charts_of_accounts.php">
    <label for="sort_by">Sort By:</label>
    <select name="sort_by" id="sort_by">
        <option value="account_id" <?php if ($order_by == 'account_id') echo 'selected'; ?>>Account ID</option>
        <option value="account_name" <?php if ($order_by == 'account_name') echo 'selected'; ?>>Account Name</option>
        <option value="account_type" <?php if ($order_by == 'account_type') echo 'selected'; ?>>Account Type</option>
        <option value="account_classification" <?php if ($order_by == 'account_classification') echo 'selected'; ?>>Account Classification</option>
    </select>
    <label for="order_dir">Order:</label>
    <select name="order_dir" id="order_dir">
        <option value="ASC" <?php if ($order_dir == 'ASC') echo 'selected'; ?>>Ascending</option>
        <option value="DESC" <?php if ($order_dir == 'DESC') echo 'selected'; ?>>Descending</option>
    </select>
    <button type="submit">Sort</button>
</form>

<!-- Table Display -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Account ID</th>
            <th>Account Name</th>
            <th>Account Type</th>
            <th>Account Classification</th>
            <th>Balance</th>
            <th>Beginning Balance</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Loop through the results and display the data
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['account_id']}</td>";
            echo "<td>{$row['account_name']}</td>";
            echo "<td>{$row['account_type']}</td>";
            echo "<td>{$row['account_classification']}</td>";
            echo "<td>{$row['balance']}</td>";
            echo "<td>{$row['beginning_balance']}</td>";
            echo "<td><a href='edit_account.php?id={$row['account_id']}'>Edit</a> | <a href='manage_charts_of_accounts.php?delete={$row['account_id']}'>Delete</a></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<?php
// Handle account deletion
if (isset($_GET['delete'])) {
    $account_id = $_GET['delete'];
    
    if (is_numeric($account_id)) {
        $delete_query = "DELETE FROM charts_of_accounts WHERE account_id = '$account_id'";
        
        if (mysqli_query($conn, $delete_query)) {
            header('Location: manage_charts_of_accounts.php'); // Redirect to avoid form re-submission
            exit(); // Stop further execution
        } else {
            echo "Error: " . mysqli_error($conn); // Display any errors during deletion
        }
    } else {
        echo "Invalid Account ID"; // Display an error if the account ID is not numeric
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Chart of Accounts</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Custom styling */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 80%;
      margin: 80px auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .header {
      text-align: center;
      margin-bottom: 40px;
    }

    .form-container {
      margin-bottom: 20px;
    }

    .form-container select,
    .form-container input,
    .form-container button {
      padding: 10px;
      margin: 10px;
      border-radius: 4px;
    }

    .form-container button {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }

    .form-container button:hover {
      background-color: #45a049;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    th,
    td {
      padding: 12px;
      text-align: center;
      border: 1px solid #ddd;
    }

    th {
      background-color: #4CAF50;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .action-buttons a {
      color: #f44336;
      font-size: 18px;
      margin-left: 10px;
    }

    .action-buttons a:hover {
      color: #ff5722;
    }

    .group-header {
      background-color: #e0e0e0;
      font-weight: bold;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="header">
      <h2>Manage Chart of Accounts</h2>
    </div>

    <!-- Form to Add New Account -->
    <div class="form-container">
      <form method="POST">
        <input type="text" name="account_id" placeholder="Account ID" required>
        <input type="text" name="account_name" placeholder="Account Name" required>
        <select name="account_type" required>
          <option value="">Select Account Type</option>
          <option value="Asset">Asset</option>
          <option value="Liability">Liability</option>
          <option value="Equity">Equity</option>
          <option value="Revenue">Revenue</option>
          <option value="Expense">Expense</option>
        </select>
        <select name="account_classification" required>
          <option value="Current">Current</option>
          <option value="Non-Current">Non-Current</option>
        </select>
        <input type="number" name="balance" placeholder="Balance" required step="0.01">
        <input type="number" name="beginning_balance" placeholder="Beginning Balance" required step="0.01">
        <button type="submit" name="add_account"><i class="fas fa-plus-circle"></i> Add Account</button>
      </form>
    </div>

    <!-- Chart of Accounts Table -->
    <table>
      <thead>
        <tr>
          <th>Account ID</th>
          <th>Account Name</th>
          <th>Account Type</th>
          <th>Balance</th>
          <th>Beginning Balance</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $current_type = '';
        while ($row = mysqli_fetch_assoc($result)) {
          // Check if the account type is changing to start a new group
          if ($current_type != $row['account_type']) {
            $current_type = $row['account_type'];
            echo "<tr class='group-header'><td colspan='6'>$current_type</td></tr>";
          }
        ?>
          <tr>
            <td><?php echo $row['account_id']; ?></td>
            <td><?php echo $row['account_name']; ?></td>
            <td><?php echo $row['account_type']; ?></td>
            <td><?php echo number_format($row['balance'], 2); ?></td>
            <td><?php echo number_format($row['beginning_balance'], 2); ?></td>
            <td class="action-buttons">
              <a href="edit_account.php?account_id=<?php echo $row['account_id']; ?>"><i class="fas fa-edit"></i></a>
              <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['account_id']; ?>)"><i class="fas fa-trash-alt"></i></a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <script>
      // JavaScript confirmation for deletion
      function confirmDelete(account_id) {
        var confirmation = confirm("Are you sure you want to delete this account?");
        if (confirmation) {
          window.location.href = "manage_charts_of_accounts.php?delete=" + account_id;
        }
      }
    </script>

  </div>

  <?php include('footer.php'); ?>

</body>

</html>
