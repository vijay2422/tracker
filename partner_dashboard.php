<!-- Add Partener Session  Start -->
<?php

session_start();
if($_SESSION['roll']!="partner"){
  $_SESSION['roll']="";
  header("Location:index.php");
  exit();
}

include('db.php');

//ExpenseID Generate..//

function generateExpenseId($connection) {
   
  $inputDate = $_POST['date']; 
  $datePart = date('dMy', strtotime($inputDate));

  
  $pattern = "%_" . $datePart;
  $pattern = mysqli_real_escape_string($connection, $pattern);

  $sql = "SELECT COUNT(*) as count FROM expense_entry ";
  // $sql = "SELECT COUNT(*) as count FROM expense_entry WHERE  user_id={$_SESSION['user_id']} AND   expense_id LIKE '$pattern'";
  $result = mysqli_query($connection, $sql);

  $countToday = 0;
  if ($row = mysqli_fetch_assoc($result)) {
      $countToday = $row['count'];
  }

  $serialNumber = str_pad($countToday + 1, 2, '0', STR_PAD_LEFT); 
  return $serialNumber . '_' . $datePart; 
}

//VOUCHER Generate..//
function generateVoucherId($connection) {
  $inputDate = $_POST['date']; 
  $monthYear = date('M-y', strtotime($inputDate)); 
  $pattern = "VOC $monthYear%";
  $pattern = mysqli_real_escape_string($connection, $pattern);

  $sql = "SELECT COUNT(*) as count FROM expense_entry ";
  $result = mysqli_query($connection, $sql);

  $count = 0;
  if ($row = mysqli_fetch_assoc($result)) {
      $count = $row['count'];
  }

  $serialNumber = str_pad($count + 1, 2, '0', STR_PAD_LEFT);
  return "VOC $monthYear $serialNumber"; 
}


// add button tedatis send in DB THIS format;

if(isset($_POST['submit'])){
 
  $expense_id=generateExpenseId($connection);
  $voucherId=generateVoucherId($connection);
  $user_id = $_SESSION['user_id'] ?? null;
  $amount=$_POST['amount'];
  $expense_date= $_POST['date'];
  $category = mysqli_real_escape_string($connection, $_POST['category']);

  $others_description=$_POST['others_description'];

  $description = mysqli_real_escape_string($connection, $_POST['description']);

  $file=$_FILES['file'];
  
$upload_files = [];
$upload_folder = 'upload/';

if (!empty($_FILES['file']['name'][0])) {
    foreach ($_FILES['file']['name'] as $key => $name) {
        $tmp_name = $_FILES['file']['tmp_name'][$key];
        $file_size = $_FILES['file']['size'][$key];

        // Check for 2MB limit (2 * 1024 * 1024 bytes)
        if ($file_size > 2 * 1024 * 1024) {
            echo "<script>alert('Error: \"$name\" is larger than 2MB. Only images less than or equal to 2MB are allowed.');</script>";
            continue;
        }

        $unique_name = time() . '-' . uniqid() . '-' . basename($name);
        $upload_path = $upload_folder . $unique_name;

        if (move_uploaded_file($tmp_name, $upload_path)) {
            $upload_files[] = $unique_name;
        } else {
            echo "<script>alert('Error: Failed to upload \"$name\".');</script>";
        }
    }
}

$file_string = implode(',', $upload_files);


  $status="Pending"; //pending............//
  $partner_invoice_number=$_POST['invoice_number'];
  $is_delete = 'no'; //default....//

$sql="INSERT INTO expense_entry(expense_id,voucher_id,user_id,amount,expense_date,category,others_description,description,filename,status,invoice_number,is_delete) 
VALUES('$expense_id','$voucherId','$user_id','$amount','$expense_date','$category','$others_description','$description','$file_string','$status',' $partner_invoice_number','$is_delete')";


  if(mysqli_query($connection,$sql)){
  
    header("Location: partner_dashboard.php#expense");
    exit;
  }
  else{
    echo "Somthing Error : ".$connection->error;
  }

}

//PAGINATION //
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startAt = $perPage * ($page - 1);

// Count total records
$countQuery = "SELECT COUNT(*) as total FROM expense_entry WHERE user_id = '".$_SESSION['user_id']."'";
$countResult = mysqli_query($connection, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $perPage);

// Main query with pagination
$query = "SELECT expense_entry.*, users.username FROM expense_entry 
          JOIN users ON expense_entry.user_id = users.id 
          WHERE expense_entry.user_id = {$_SESSION['user_id']}
          ORDER BY expense_date DESC 
          LIMIT $startAt, $perPage";
          

$result = mysqli_query($connection, $query);

// Pagination links


?>

<!-- Add Partener Session  End -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PartnerrDashbord</title>
  <link rel="stylesheet" href="css/partner_dashbord.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="script.js"></script>
  
  
 
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-none d-md-block sidebar p-3">
        <h4 class="text-white"> Partner Panel</h4>
        <ul class="nav flex-column mt-4">
          <li class="nav-item">
            <a class="nav-link active" style="color:white;" href="#" onclick="showSection('dashboard')"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" style="color:white;" onclick="showSection('expense')"><i class="bi bi-cash-coin me-2"></i> Partner Expenses</a>
          </li>
         
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-md-10 ms-sm-auto px-md-4 py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 id="section-title">Tidy  Dashboard</h2>
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
              <img src="img/img.jpg" alt="" class="round-circle" style="width: 20px; height: 20px;">
              <?php echo $_SESSION['username']?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="change_password.php">Change Password</a></li>
              <li><hr class="dropdown-divider" /></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </div>
        </div>

        <!-- Dashboard Section -->
        <!-- <div id="dashboard" class="content-section active"> -->
            <div id="dashboard" class="content-section active">
          
        </div>
        
        <!-- Expense Section -->
        <div id="expense" class="content-section">
        

          <div class="container-fluid p-5">
        <div class="row">
            <div class="col-12">
            
                <div class="card mt-5">
                    <div class="card-header"style="background:#0148B7;color:white" >
                        <h1 class="text-center">  PARTNER EXPENSES </h1>
                    </div>
<div class="card-body">
  
<!-- Button trigger modal -->
<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
 
 <i class="fa-solid fa-plus"></i> Add Expenses
</button>

<form action="" method="POST">
<div class="row mt-5 align-items-end">

  <div class="col-md-3">
   
    <select class="form-select" id="choose" name="choose" required>
      <option value="">Choose...</option>
      <option value="me">Me</option>
      <option value="others">Others</option>
      <option value="all">All</option>
    </select>
    <div class="invalid-feedback">
      Please select a valid option.
    </div>
  </div>


  <div class="col-md-3">
   
    <input type="month" id="month" name="month" class="form-control">
  </div>

 
  <div class="col-md-1">
    <button type="submit" id="search" name="search" style="
    margin-left: -24%"; class="btn btn-primary w-10" >
      <i class="fas fa-search"></i> 
    </button>
  </div>
</button>
</div>
</form>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Expense Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      
       
<form action=""  method="POST" enctype="multipart/form-data">

<div class="mb-3">
    <label for="invoice_number" class="form-label">Invoice Number <span  style="color:red;">*</span></span></label>
    <input type="text" class="form-control" id="invoice_number" name="invoice_number" placeholder="Enter the Invoice number or Transaction_id or N/A..." required  >
  </div>

  <div class="mb-3">
    <label for="amount" class="form-label">Amount <span  style="color:red;">*</span></span></label>
    <input type="number" class="form-control" id="amount" name="amount" placeholder=" ₹ Enter the Amount.." required >
  </div>
  <div class="mb-3">
    <label for="date" class="form-label">Date <span  style="color:red;">*</span></span></label>
    <input type="date" class="form-control" id="date" name="date" required>
  </div>


<div class="mb-3">
   
 
  <label for="category">Category <span  style="color:red;">*</span> </label>
  <select id="category" name="category" class="form-control" onchange="toggleDescriptions()" required>
      <option value="">--Select--</option>
      <option value="food">Food</option>
      <option value="travel">Travel</option>
      <option value="office_supplies">Office Supplies</option>
      <option value="others">Others</option>
  </select>

    <div id="other-description" style="display:none; margin-top: 10px;">
      <label for="other_description">Others Category <span  style="color:red;">*</span></label>
      <textarea name="others_description" id="other_description" class="form-control" placeholder="Enter Category.." style="resize: none;"></textarea>
    </div>
  <?php
  


  ?>

   

</div>
  <div class="mb-3">
    <label for="description" class="form-label">Description</label>

     <textarea name="description" id=" description" class="form-control"  placeholder="Enter the description.." style="resize:none"></textarea>
  </div>

<div class="mb-3">
    <label for="file">File <span style="color:red;">*</span></label>
    <input type="file" class="form-control" id="file" name="file[]" accept=".jpeg,.jpg,.png,.gif,.pdf" onchange="validateFiles(this)" required>
    <div class="invalid-feedback" id="file-error" style="display: none;">
        Only images and PDFs less than 2MB are allowed. DOC, HTML, and other files are not allowed.
    </div>
</div>

  <button type="submit" name="submit" class="btn btn-success" value="submit">Submit</button>

</form>
</div>
     
    </div>
  </div>
</div>
</div>

<!-- //second-table// -->

  <div class="card-body ">
  
  <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
  <table class="table table-bordered " >
  <thead class="bg-secondary text-white text-center">
    <tr>
      <th>S.No</th>
      <th scope="col">EXPENSE ID</th>
      <!-- <th scope="col">VOUCHER ID</th> -->
      <!-- <th scope="col">USER ID</th> -->
      <th scope="col">NAME</th>
      <th scope="col">AMOUNT</th>
      <th scope="col">DATE</th>
      <th scope="col">CATEGORY</th>
      <th scope="col">INVOICE NUMBER</th>
      <th scope="col">DESCRIPTION</th>
      <th scope="col">ATTACHMENT</th>
      <th scope="col">STATUS</th>
      <th scope="col">VOUCHER</th>
    </tr>
   
  </thead>
  <tbody>
  <?php

if ($connection->connect_error) {
    die("Connection Failed: " . $connection->connect_error);
}
$user_id = $_SESSION['user_id'] ?? null;

// $sql = "SELECT * FROM expense_entry  WHERE user_id={$_SESSION['user_id']} And SELECT users.*,users.username FROM users ";
$sql = "SELECT expense_entry.*, users.username FROM expense_entry JOIN users ON expense_entry.user_id = users.id 
        WHERE expense_entry.user_id = {$_SESSION['user_id']}  ORDER BY expense_entry.id DESC ";
       
        
       


//Filtering in choose .. Option// 

if(isset($_POST['search'])){
  if($_POST['choose']=='me'){
    // $sql = "SELECT * FROM expense_entry  WHERE user_id={$_SESSION['user_id']}";
    // $sql = "SELECT expense_entry.*, admin.name ,admin.email FROM expense_entry JOIN users ON expense_entry.user_id = users.id 
    //         WHERE expense_entry.user_id = {$_SESSION['user_id']}";
    // $sql = "SELECT expense_entry.*, users.username, users.email FROM expense_entry JOIN users ON expense_entry.user_id = users.id WHERE expense_entry.user_id = {$_SESSION['user_id']}
    //  ORDER BY expense_entry.id DESC";
      $sql .= " LIMIT $startAt, $perPage";
    $result = $connection->query($sql);


  }
  if($_POST['choose']=='others'){

    //$sql = "SELECT * FROM expense_entry  WHERE user_id!={$_SESSION['user_id']}";
    $sql = "SELECT expense_entry.*, users.username FROM expense_entry 
            JOIN users ON expense_entry.user_id = users.id WHERE expense_entry.user_id != {$_SESSION['user_id']} ORDER BY expense_entry.id DESC";
  }

  if($_POST['choose']=='all'){
    $sql = "SELECT expense_entry.*, users.username
            FROM expense_entry JOIN users ON expense_entry.user_id = users.id  ORDER BY expense_entry.id DESC";
  }
  $input = $_POST['month']; 
  if(!empty($input)){
    
    $formattedMonth =$input;
    if(($_POST['choose']!='all')){
    $sql.= " AND expense_date LIKE '$formattedMonth%' ORDER BY expense_entry.id DESC";
    }
    else{
      // $sql.= " WHERE expense_date LIKE '$formattedMonth%' ORDER BY expense_entry.id DESC";
      $sql .= " WHERE expense_date LIKE '" . $formattedMonth . "%' ORDER BY expense_entry.id DESC";

    }
  }
 
}
$result = $connection->query($sql);// exqute query

// if ($result->num_rows > 0) {
  // if(mysqli_num_rows($result)> 0) {
  if ($result && mysqli_num_rows($result) > 0){
   
    $id=1;
    while ($row = mysqli_fetch_array($result)) {
        $uid = $row['id'];
        $status = $row['status'];
        $expenseId = $row['expense_id']; 
        $voucherId=$row['voucher_id'];
        $user_id = $row['user_id'];
        $username= $row['username'];
        $amount = $row['amount'];
        $date = $row['expense_date'];
        $formattedDate = date("d-m-Y", strtotime($date));
        $category = $row['category'];
        $partner_invoice_number=$row['invoice_number'];
        $description = $row['description'];
        $filename = $row['filename'];
        $modalId = "modal_" . $expenseId;

        if($status=="Pending"){
          $status_name="Pending";
         }
         elseif($status=="Rejected"){
           $status_name="Rejected";
         }
         elseif($status=="Approved"){
           $status_name="Approved";
         }

?>
<tr class="text-center">

  <td><?php echo $id?></td>
  <td><?php echo $expenseId?></td>
  <!-- <td><?php echo $user_id ?></td> -->
  <td><?php echo  $username ?></td>
  <td><?php echo $amount ?></td>
  <td><?php echo $formattedDate ?></td>
  <td><?php echo $category ?></td>
  <td><?php echo $partner_invoice_number ?></td>
  <td class="description" title="<?php echo $description; ?>"><?php echo $description; ?></td>

  <td>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-success " data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
    <i class="fa-regular fa-eye"></i> 
    </button>&nbsp;

    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Invoice  File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
           <div style="max-height: 400px; overflow-y: auto;">
                      <h6><?php echo "ExpenseId :".$expenseId; ?></h6>
                      <h6><?php echo "UserName :".$username; ?></h6>
                      <h6><?php echo "Amount :".$amount; ?></h6>
              <?php
                $filePath = 'upload/' .$filename;
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo '<img src="' . $filePath . '" class="img-fluid rounded" alt="Image Preview">';
                } elseif ($extension === 'pdf') {
                    echo '<embed src="' . $filePath . '" type="application/pdf" width="100%" height="600px" />';
                } else {
                    echo '<p class="text-danger">Unsupported file type.</p>';
                }
              ?>
          </div>
      </div>
    </div>
  </div>
</div>

    

<?php
if($user_id==$_SESSION['user_id']){
if($status_name != "Approved") {
  echo '<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal_'.$expenseId .'">
  <i class="fa-solid fa-pen-to-square text-dark"></i>
  </button>';
}

} 
?>

<!-- Edit Modal -->
<div class="modal fade" id="editModal_<?php echo $expenseId; ?>" tabindex="-1" aria-labelledby="editModalLabel_<?php echo $expenseId; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <form action="update.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel_<?php echo $expenseId; ?>">Update Expense</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="expense_id" value="<?php echo $expenseId; ?>">

        <div class="mb-3">
          <label for="amount_<?php echo $expenseId; ?>" class="form-label d-flex">Amount  <spam style="color:red;"> *</spam></label>
          <input type="text" class="form-control" name="amount" id="amount_<?php echo $expenseId; ?>" value="<?php echo $amount; ?>" required>
        </div>

        <div class="mb-3">
          <label for="date_<?php echo $expenseId; ?>" class="form-label d-flex">Date <spam style="color:red;"> *</spam></label>
          <input type="date" class="form-control" name="date" id="date_<?php echo $expenseId; ?>" value="<?php echo $date; ?>" required>
        </div>

        <div class="mb-3">
          <label for="category_<?php echo $expenseId; ?>" class="form-label d-flex">Category <spam style="color:red;"> *</spam></label>
          <select name="category" id="category_<?php echo $expenseId; ?>" class="form-select" required>
            <option value="food" <?php if($category == "food") echo "selected"; ?>>Food</option>
            <option value="travel" <?php if($category == "travel") echo "selected"; ?>>Travel</option>
            <option value="office_supplies" <?php if($category == "office_supplies") echo "selected"; ?>>Office Supplies</option>
            <option value="others" <?php if($category == "others") echo "selected"; ?>>Others</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="description_<?php echo $expenseId; ?>" class="form-label d-flex">Description <spam style="color:red;"> *</spam></label>
          <textarea name="description" id="description_" class="form-control" placeholder="Enter the description..." style="resize:none"><?php echo $description; ?></textarea>

        </div>

        <div class="mb-3">
          <label for="file_<?php echo $expenseId; ?>" class="form-label d-flex">File<spam style="color:red;"> *</spam></label>
          <input type="file" class="form-control" name="file[]" id="file_<?php echo $expenseId; ?>" >
        </div>
      </div>
      <input type="hidden" name="id" value="<?php echo $expenseId; ?>">

      <div class="modal-footer">
        <button type="submit" name="update" class="btn btn-primary" style="margin-right: 81%;">Update</button>
      </div>
    </form>
  </div>
</div>


<!--  Image  and view   Modal -->
    <!-- Modal -->
    <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="<?php echo $modalId; ?>Label" aria-hidden="true">
      <div class="modal-dialog  modal-lg">
        <div class="modal-content">

          <div class="modal-header"> 
            <h5 class="modal-title" id="<?php echo $modalId; ?>Label">Invoice Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
             <div style="max-height: 400px; overflow-y: auto;">
            <div class="d-flex flex-column">
              <div><strong>Expense_ID :</strong> <?php echo $expenseId; ?></div>
               <div><strong> Name :</strong> <?php echo $username; ?></div>
                <div><strong>Amount :</strong> <?php echo $amount; ?></div>

              <?php if (!empty($filename)) {
              $files = explode(',', $filename);
              foreach ($files as $file) {
              ?>
               <div><img src="upload/<?php echo $file; ?>" class="img-fluid mt-2" alt="Expense Image" style="width: 400px; height: 400px; object-fit: cover;">
              </div>
              </div>
<?php
    }
    $id++;   
} else {
?>
    <div>No image available.</div>
<?php } ?>

            </div>

          </div>

        </div>
</div>
      </div>
 
    </div>
  </td>
  
  <!-- <td><?php echo $status_name ?></td> -->
  <td>
      <?php 
        if($status_name=="Approved"){
          echo $status_name ;
        }
       


     ?>
  </td>
<td>
    <?php
      if($user_id==$_SESSION['user_id']){
        if( $status_name=="Pending"){
          echo '<button class="btn btn-primary" disabled><i class="bi bi-receipt"></i> Voucher</button>';
        }
        elseif($status_name=="Approved"){ 
          echo '<a href="voucher.php?expense_id='.$expenseId.'" class="btn btn-primary" target="_blank">
          <i class="bi bi-receipt"></i> Voucher </a>';
        }
      }
      
    ?>
</td>
</tr>
<?php
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>No records found.</td></tr>";
}
?>
  </tbody>
</table>
</div>

</div>
</div>
 </div>
</div>

<!-- //PAGINATION// -->
<!-- Pagination -->
<nav aria-label="Page navigation">
    <div class="d-flex justify-content-center mt-5">
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="partner_dashboard.php?page=<?php echo $page - 1; ?>#expense">Previous</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="partner_dashboard.php?page=<?php echo $i; ?>#expense"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="partner_dashboard.php?page=<?php echo $page + 1; ?>#expense">Next</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

        </div>

        <!-- Users Section -->
        <div id="users" class="content-section">

        </div>

  <script>
    function showSection(sectionId) {
      // Hide all content sections
      const sections = document.querySelectorAll(".content-section");
      sections.forEach((sec) => sec.classList.remove("active"));

      // Show selected section
      document.getElementById(sectionId).classList.add("active");
         

      // Update section title
      document.getElementById("section-title").innerText = sectionId.charAt(0).toUpperCase() + sectionId.slice(1);

      // Highlight active sidebar link
      const links = document.querySelectorAll(".sidebar a");
      links.forEach(link => link.classList.remove("active"));
      event.target.classList.add("active");
    }

   document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash;
    if (hash) {
      const sectionId = hash.substring(1); // remove #
      if (document.getElementById(sectionId)) {
        showSection(sectionId); // call your existing function
        // Optionally scroll to the section
        document.getElementById(sectionId).scrollIntoView({ behavior: "smooth" });
      }
    }
  });








   

    function validateName(input) {
      const nameError = document.getElementById("nameError");
      const regex = /^[a-zA-Z\s]*$/;
      
      if (!regex.test(input.value)) {
        nameError.style.display = "block";
        input.setCustomValidity("Only letters are allowed");
      } else {
        nameError.style.display = "none";
        input.setCustomValidity("");
      }
    }

    // Run on page load in case "others" is pre-selected
    window.onload = function() {
      toggleDescription();
    };
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>