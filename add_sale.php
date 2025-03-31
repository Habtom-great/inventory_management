<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Sales Invoice</title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

 <style>
 body {
  background-color: #f4f4f9;
 }

 .card {
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
 }

 .summary-section {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 5px;
  margin-top: 20px;
  text-align: right;
  font-size: 1.1rem;
  font-weight: bold;
 }

 .amount-in-words {
  font-style: italic;
  font-weight: bold;
  color: #007bff;
  margin-top: 10px;
 }

 table th,
 table td {
  text-align: center;
  vertical-align: middle;
 }
 </style>
</head>

<body>

 <div class="container mt-2">
  <h2 class="mb-4 text-center">Sales Invoice</h2>
  <div class="card p-4">
   <form action="add_sale.php" method="POST" id="sales_form">
    <div class="row">
     <div class="col-md-2">
      <label for="sales_order_no" class="form-label">Sales Order No.</label>
      <input type="text" class="form-control" id="sales_order_no" name="sales_order_no" required>
     </div>
     <div class="col-md-2">
      <label for="invoiceDate" class="form-label">Invoice Date:</label>
      <input type="date" class="form-control" id="invoiceDate" name="invoiceDate" required>
     </div>
     <div class="col-md-2">
      <label for="sales_invoice_no" class="form-label">Invoice No.</label>
      <input type="text" class="form-control" id="sales_invoice_no" name="sales_invoice_no" required>
     </div>
     <div class="col-md-2">
      <label for="Reference" class="form-label">Reference No.</label>
      <input type="text" class="form-control" id="reference" name="reference" required>
     </div>
    </div>
    <div class="row">
     <div class="col-md-2">
      <label for="tin_no" class="form-label">Customer Tin No</label>
      <input type="text" class="form-control" id="tin_no" name="tin_no" required>
     </div>

     <div class="col-md-2">
      <label for="customer_company_name" class="form-label">Customer Company Name</label>
      <input type="text" class="form-control" id="customer_company_name" name="customer_company_name" required>
     </div>
     <div class="col-md-2">
      <label for="telephone" class="form-label">Customer Tel.No.</label>
      <input type="text" class="form-control" id="telephone" name="telephone" required>
     </div>
     <div class="col-md-2">
      <label for="job id" class="form-label">Job ID</label>
      <input type="text" class="form-control" id="job id" name="job id" required>
     </div>
    </div>


    <table id="item_table" class="table table-bordered mt-2">
     <thead>
      <tr>
       <th>Item ID</th>
       <th>Description</th>
       <th>Quantity</th>
       <th>Unit Price</th>
       <th>Total</th>
       <th>Action</th>
      </tr>
     </thead>
     <tbody>
      <tr>
       <td><input type="text" class="form-control item_id" required></td>
       <td><input type="text" class="form-control description" required></td>
       <td><input type="number" class="form-control quantity" required></td>
       <td><input type="number" class="form-control price" step="0.01" required></td>
       <td><input type="text" class="form-control total" readonly></td>
       <td><button type="button" class="btn btn-danger removeRow">X</button></td>
      </tr>
     </tbody>
    </table>

    <button type="button" class="btn btn-primary" id="add_item">Add Item</button>

    <div class="summary-section mt-3">
     <p>Subtotal: <span id="subtotal">0.00</span></p>
     <p>VAT (15%): <span id="vat">0.00</span></p>
     <p><strong>Total: <span id="grand_total">0.00</span></strong></p>
     <p class="amount-in-words" id="amount_in_words">Total in Words: Zero Birr only.</p>
    </div>

    <button type="submit" class="btn btn-success mt-3">Submit</button>
   </form>
  </div>
 </div>


 <script>
 function updateSummary() {
  let subtotal = 0;
  $('.total').each(function() {
   subtotal += parseFloat($(this).val()) || 0;
  });

  let vat = subtotal * 0.15;
  let grandTotal = subtotal + vat;

  $('#subtotal').text(subtotal.toLocaleString(undefined, {
   minimumFractionDigits: 2
  }));
  $('#vat').text(vat.toLocaleString(undefined, {
   minimumFractionDigits: 2
  }));
  $('#grand_total').text(grandTotal.toLocaleString(undefined, {
   minimumFractionDigits: 2
  }));
  $('#amount_in_words').text("Total in Words: " + toWordsWithCents(grandTotal));
 }

 function toWordsWithCents(num) {
  const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
  const teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
   "Nineteen"
  ];
  const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
  const thousands = ["", "Thousand", "Million", "Billion"];

  function convertHundreds(num) {
   let result = "";
   if (num >= 100) {
    result += ones[Math.floor(num / 100)] + " Hundred ";
    num %= 100;
   }
   if (num >= 10 && num < 20) {
    result += teens[num - 10] + " ";
   } else {
    result += tens[Math.floor(num / 10)] + " " + ones[num % 10] + " ";
   }
   return result.trim();
  }

  function convert(num) {
   if (num === 0) return "Zero Birr only";
   let words = "";
   let place = 0;

   while (num > 0) {
    if (num % 1000 !== 0) {
     words = convertHundreds(num % 1000) + " " + thousands[place] + " " + words;
    }
    num = Math.floor(num / 1000);
    place++;
   }
   return words.trim();
  }

  let birr = Math.floor(num);
  let cents = Math.round((num - birr) * 100);

  let birrWords = convert(birr) + " Birr";
  let centsWords = cents > 0 ? " and " + convert(cents) + " Cents" : "";

  return birrWords + centsWords + " only";
 }

 $(document).on('input', '.quantity, .price', function() {
  let row = $(this).closest('tr');
  let qty = parseFloat(row.find('.quantity').val()) || 0;
  let price = parseFloat(row.find('.price').val()) || 0;
  row.find('.total').val((qty * price).toFixed(2));
  updateSummary();
 });

 $('#add_item').click(function() {
  let newRow = $('#item_table tbody tr:first').clone();
  newRow.find('input').val('');
  $('#item_table tbody').append(newRow);
 });

 $(document).on('click', '.removeRow', function() {
  $(this).closest('tr').remove();
  updateSummary();
 });
 </script>

</body>

</html>