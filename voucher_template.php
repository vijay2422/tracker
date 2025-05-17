
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Expense Voucher</title>
  <link rel="stylesheet" href="css./voucher.css">
  <style>
   
  </style>
</head>
<body>
  <div class="voucher">
    <div class="voucher-header">
      <h1> Tidyds Digital Soluction</h1>
      <h1>Expense Voucher</h1>
      <p>Voucher No: <strong><?= htmlspecialchars($voucherId) ?></strong></p>
    </div>

    <table>
      <tr>
        <th>Voucher ID</th>
        <td><?= htmlspecialchars($voucherId) ?></td>
      </tr>
      <tr>
        <th>Expense ID</th>
        <td><?= htmlspecialchars($expenseId) ?></td>
      </tr>
      <tr>
        <th>Partner Name</th>
        <td><?= htmlspecialchars($username) ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?= htmlspecialchars($email) ?></td>
      </tr>
      <tr>
        <th>Date</th>
        <td><?= htmlspecialchars($correct_format_date) ?></td>
      </tr>
      <tr>
        <th>Amount</th>
        <td><?= "₹" . number_format($amount, 2) ?></td>
      </tr>
      <tr>
        <th>Category</th>
        <td><?= htmlspecialchars($category) ?></td>
      </tr>
      <tr>
        <th>Description</th>
        <td class="rowspan=1"><?= htmlspecialchars($description) ?></td>
      </tr>
      <tr>
        <th>Status</th>
        <!-- <td><?= htmlspecialchars($status_name) ?></td> -->
        <td><?= htmlspecialchars($status) ?></td>
      </tr>
    </table>

    <div class="footer">
      This is a system-generated voucher. No signature required.<br />
      Partner Expense App © 2025
    </div>
  </div>
</body>
</html>
