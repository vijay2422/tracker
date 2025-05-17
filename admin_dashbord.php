<!-- //Admin User Session  Start// -->

<?php
session_start();

if($_SESSION['roll']!="admin"){
    $_SESSION['roll']="";
    header("Location:index.php");
    exit();
}

// Include Composer's autoloader
require 'vendor/autoload.php';
include('db.php');

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
   
    $name = $_POST['name'];
    $email = $_POST['email'];
    $roll = $_POST['roll'];
  
    
    $designation = ($roll === 'admin') ? 'Administrator' : 'Partner';
    

    // Generate temporary password
    function generateRandomPassword($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($characters), 0, $length);
    }

    function sendEmployeeEmail($employeeData, $connection) {
        $mail = new PHPMailer(true);
        try {
            $tempPassword = generateRandomPassword();
            
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'vijaylakshman2422@gmail.com'; 
            $mail->Password = 'tlrhyyyshfvbazcz'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $sendEmail = $employeeData['email'];
            
            $mail->setFrom($sendEmail, 'Tidy Digital Solutions');
            $mail->addAddress($employeeData['email']);

            // Embed image
            $mail->AddEmbeddedImage('./img/logo.png', 'logoImage');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Tidy Digital Solutions';
            $body = '<!DOCTYPE html>
<html lang="en">
<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f6f6f6;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px;">
        
        <div style="text-align: center; padding-bottom: 20px;">
            <img src="cid:logoImage" alt="Tidy Digital Solutions" style="max-width: 150px;" />
            <h1 style="font-size: 24px; color: #333333;">Welcome to the Team, '.$employeeData['name'].'!</h1>
          
        </div>
        
        <div style="font-size: 15px; color: #444444; line-height: 1.6;">
            <p>Thank you for joining Tidy Digital Solutions! Below are your temporary credentials to access our systems. Please keep this information secure and follow the instructions for your first login.</p>
            
            <table style="width: 100%; margin: 20px 0; border-collapse: collapse;">
                <tr>
                    <td style="font-weight: bold; padding: 8px 0;">Email Address:</td>
                    <td style="padding: 8px 0;">'.$employeeData['email'].'</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 8px 0;">Temporary Password:</td>
                    <td style="padding: 8px 0;">'.$tempPassword.'</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 8px 0;">Position:</td>
                    <td style="padding: 8px 0;">'.$employeeData['designation'].'</td>
                </tr>
            </table>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="http://localhost/record/index.php" style="background-color: #007bff; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Access Portal</a>
            </div>
            
            <h3 style="color: #333333;">Important Security Information</h3>
            <p><strong>First Login:</strong> You\'ll be required to update your password and complete your profile setup during first login.</p>
            <p><strong>Security Reminder:</strong> Never share your credentials. Our support team will never ask for your password via email or phone.</p>
            <p><strong>Need Help?</strong> Contact <a href="mailto:support@tidyds.com">business@tidyds.com</a> for any login assistance.</p>

            <p style="text-align: center; margin-top: 40px; color: #777777;">We look forward to working with you and achieving great things together!</p>
        </div>
        
        <div style="text-align: center; border-top: 1px solid #dddddd; margin-top: 30px; padding-top: 20px; font-size: 13px; color: #999999;">
            <img src="cid:logoImage" alt="Tidy Digital Solutions" style="max-width: 100px; margin-bottom: 10px;" />
            <p>© '.date('Y').' Tidy Digital Solutions. All rights reserved.</p>
            <p>7/85-1 J.C.B Back Side, Chinnaseeragapadi, Salem, Tamilnadu, Salem-636308.</p>
            <p>
                <a href="https://tidyds.com" style="color: #999999; text-decoration: none;">Visit Our Website</a> |
                <a href="mailto:business@tidyds.com" style="color: #999999; text-decoration: none;">Contact Us</a>
            </p>
        </div>
    </div>
</body>
</html>'
;
            
            $mail->Body = $body;

            $mail->send();
            
            // Return both success status and temp password
            return ['status' => 'success', 'temp_password' => $tempPassword];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => "Mailer Error: {$mail->ErrorInfo}"];
        }
    }

    // Employee data
    $employeeData = [
        'name' => $name,
        'email' => $email,
        'designation' => $designation,
        'roll' => $roll
    ];
    
    // Check if email exists
    $mysqlexcute = "SELECT * FROM users WHERE email='$email'";
    $mysqlreult = mysqli_query($connection, $mysqlexcute);

    if(mysqli_num_rows($mysqlreult) == 1) {
        echo '
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
          <div id="emailExistsToast" class="toast align-items-center text-white bg-danger border-0 shadow rounded-3" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px;">
            <div class="d-flex">
              <div class="toast-body">
                <strong>Warning!</strong> Mail ID already exists.
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener("DOMContentLoaded", function(){
            var toastEl = document.getElementById("emailExistsToast");
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
          });
        </script>';
    } else {
        // Send email and get result
        $result = sendEmployeeEmail($employeeData, $connection);
        
        if ($result['status'] === 'success') {
            $tempPassword = $result['temp_password'];
            $passwordmd5 = md5($tempPassword);

            // Insert into database
            $sql = "INSERT INTO users (`username`, `email`, `roll`, `password`,`Action`) VALUES ('$name','$email','$roll','$passwordmd5','Active')";
            
            if(mysqli_query($connection, $sql)) {
                // Show success message and redirect
                echo '
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                  <div id="successToast" class="toast align-items-center text-white bg-success border-0 shadow rounded-3" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px;">
                    <div class="d-flex">
                      <div class="toast-body">
                        <strong>Success!</strong> Email sent successfully and user created!
                      </div>
                      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                  </div>
                </div>
                <script>
                  document.addEventListener("DOMContentLoaded", function(){
                    var toastEl = document.getElementById("successToast");
                    var toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    
                    // Redirect after 3 seconds
                    setTimeout(function() {
                      window.location.href = "admin_dashbord.php";
                    }, 3500);
                  });
                </script>';
            } else {
                echo "Database Error: ".$connection->error;
            }
        } else {
            echo $result['message'];
        }
    }
}

//Admin User Session  End//

//Admin Expense  Session  Start//

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

if(isset($_POST['submit2'])){
 
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

 $status="Approved";

  $invoicess_number=$_POST['invoice_numbers'];
  $is_delete="No";
  $sql="INSERT INTO expense_entry(expense_id,voucher_id,user_id,amount,expense_date, category,others_description,description,filename,status,invoice_number,is_delete) 
  VALUES('$expense_id','$voucherId','$user_id','$amount','$expense_date','$category','$others_description','$description','$file_string','$status', '$invoicess_number','$is_delete')";
  
  
    if(mysqli_query($connection,$sql)){
      echo "<div class='success-message'>Form submitted successfully!</div>";
        echo '<script>document.getElementById("yourFormId").reset();</script>';
      // echo "<script>location.replace(admin_expense.php')</script>";
      // header("Location: admin_dashbord.php/#expense");
      // header("Location: admin_dashbord.php/#expense");
       header("Location: admin_dashbord.php");
      exit;
      
    }
    else{
      echo "Somthing Error : ".$connection->error;
    }


//ADMIN DASHBORD// 
// CHOOSE OPTION  ADMIN AND PARTNER  FILTERING NAME AND MONTH WISE FILTERING//

if(isset($_POST['search_btn'])){
  $choose=$_POST['choose'];
  $choose_name=$_POST['choose_name'];
  $month=$_POST['month'];
  if($choose=="admin"){
    $sql="SELECT expense_entry.* from expense_entry where user_id=(SELECT id from users WHERE username='$choose_name') AND expense_date LIKE'$month%' AND is_delete='No' ORDER BY expense_date DESC";

  }
  else if($choose=="partner"){
    $sql="SELECT expense_entry.* from expense_entry where user_id=(SELECT id from users WHERE username='$choose_name') AND expense_date LIKE'$month%' AND is_delete='No' ORDER BY expense_date DESC";

  }
  else{
    
  }

}
$result = $connection->query($sql);// exqute query


  }
///Pagination in  Add Expense Entry//

  // $perPage = 10;
  // $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
  // $startAt = $perPage * ($page - 1);
  
  // // Get total count of records
  // $countQuery = "SELECT COUNT(*) as total FROM  expense_entry WHERE user_id = '".$_SESSION['user_id']."'";

  // $countResult = mysqli_query($connection,$countQuery);

  // $totalData = mysqli_fetch_assoc($countResult);
  // $totalRecords = $totalData['total'];
  
  // // Calculate total pages
  // $totalPages = ceil($totalRecords / $perPage);

  // // Get paginated data
  // $query = "SELECT * FROM expense_entry WHERE user_id = '" . $_SESSION['user_id'] . "' ORDER BY create_at  DESC LIMIT $startAt, $perPage";
  // $result = mysqli_query($connection, $query);
  
  // // Check if the query was successful
  // if (!$result) {
  //     echo "Query failed: " . mysqli_error($connection);
  //     echo "<br>Query: " . $query;
  //     exit; 
  // }
  
  // // Display your records here
  // while ($row = mysqli_fetch_assoc($result)) {
  //   echo"<tr>";
  //   echo"<td>".$row['id']."</td> .<br>";
  //    echo"<td>".$row['expense_id']."</td>";
     
  // }

// $perPage = 10;
// $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
// $startAt = $perPage * ($page - 1);

// // Escape user_id to prevent SQL injection (though it's from session, it's still good practice)
// $userId = mysqli_real_escape_string($connection, $_SESSION['user_id']);

// // Get total number of records
// $countQuery = "SELECT COUNT(*) as total FROM expense_entry WHERE user_id = '$userId'";
// $countResult = mysqli_query($connection, $countQuery);

// if (!$countResult) {
//     echo "Count query failed: " . mysqli_error($connection);
//     exit;
// }

// $totalData = mysqli_fetch_assoc($countResult);
// $totalRecords = $totalData['total'];
// $totalPages = ceil($totalRecords / $perPage);

// // Get paginated data
// $query = "SELECT * FROM expense_entry WHERE user_id = '$userId' ORDER BY create_at DESC LIMIT $startAt, $perPage";
// // print_r($query);
// // die();
// $result = mysqli_query($connection, $query);

// if (!$result) {
//     echo "Data query failed: " . mysqli_error($connection);
//     echo "<br>Query: " . $query;
//     exit;
// }

$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Ensure page is at least 1
$startAt = $perPage * ($page - 1);

// Escape user_id to prevent SQL injection
$userId = mysqli_real_escape_string($connection, $_SESSION['user_id']);

// Get total number of records
$countQuery = "SELECT COUNT(*) as total FROM expense_entry WHERE user_id = '$userId'";
$countResult = mysqli_query($connection, $countQuery);

if (!$countResult) {
    die("Count query failed: " . mysqli_error($connection));
}

$totalData = mysqli_fetch_assoc($countResult);
$totalRecords = $totalData['total'];
$totalPages = max(1, ceil($totalRecords / $perPage)); // Ensure at least 1 page

// Get paginated data
$query = "SELECT * FROM expense_entry WHERE user_id = '$userId' ORDER BY create_at DESC LIMIT $startAt, $perPage";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Data query failed: " . mysqli_error($connection) . "<br>Query: " . $query);
}

  
  
  

//Admin Expense Session  End//



?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/dashbord.css">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
     <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="script.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<!-- //Dashbord Count css // -->

<style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      
    }

    .status-container {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .status-card {
      background-color:white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.2);
      width: 350px;
      text-align: center;
      transition: transform 0.2s ease;
    }

    .status-card:hover {
      transform: translateY(-5px);
    }

    .badge {
      display: inline-block;
      padding: 17px 70px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: bold;
      color: white;
    }

    .pending {
      background-color: #ff9800;
    }

    .approved {
      background-color: #4caf50;
    }

    .rejected {
      background-color: #f44336;
    }
    .all{
        background-color: #0148B7;
    }
    h3 {
      margin-bottom: 10px;
    }

    p {
      /* color: #555; */
      color: black;
    }

  </style>
 
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-2 d-none d-md-block sidebar p-3">
        <h4 class="text-white">Admin Panel</h4>
        <ul class="nav flex-column mt-4">
          <li class="nav-item">
            <a class="nav-link " href="#" onclick="showSection('dashboard')"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="showSection('expense')"><i class="bi bi-cash-coin me-2"></i> My Expenses</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="#" onclick="showSection('users')"><i class="bi bi-people me-2"></i> Partners</a>
          </li>
         
        </ul> 
      </nav>

      <!-- Main Content -->
      <main class="col-md-10 ms-sm-auto px-md-4 py-4" >
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 id="section-title">TIDY Expense Dashboard</h2>
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <!-- <i class="bi bi-person-circle"></i> Admin --> 
              <img src="img/img.jpg" alt="" class="round-circle" style="width: 20px; height: 20px;">
              <?php echo $_SESSION['username']?>

            </button>
            <ul class="dropdown-menu dropdown-menu-end">
           
              <li><a class="dropdown-item" href="change_password.php">Change Password</a></li>
              <li><hr class="dropdown-divider" /></li>
              <li><a class="dropdown-item" href="index.php">Logout</a></li>
            </ul>
          </div>
        </div>

        <!-- Dashboard Section -->
        <div id="dashboard" class="content-section active">
        <div class="card-body">
                        <!-- <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fa-solid fa-plus"></i> Add Expense
                        </button> -->
<!-- ///start  on  pending and approved and rejected// -->

<!-- //Count// -->
<?php

$Pending = "SELECT COUNT(*) as pending_count FROM expense_entry WHERE status='Pending'";
$Approved = "SELECT COUNT(*) as approved_count FROM expense_entry WHERE status='Approved'";
$Rejected = "SELECT COUNT(*) as rejected_count FROM expense_entry WHERE status='Rejected'";
$All = "SELECT COUNT(*) as all_count FROM expense_entry";

$pendingResult = mysqli_query($connection, $Pending);
$approvedResult = mysqli_query($connection, $Approved);
$rejectedResult = mysqli_query($connection, $Rejected);
$allResult = mysqli_query($connection, $All);

$pendingCount = mysqli_fetch_assoc($pendingResult)['pending_count'];
$approvedCount = mysqli_fetch_assoc($approvedResult)['approved_count'];
$rejectCount = mysqli_fetch_assoc($rejectedResult)['rejected_count'];
$allCount = mysqli_fetch_assoc($allResult)['all_count'];
?>


<div class="status-container mt-5">
    <div class="status-card" onclick="filterStatus('Pending')">
        <h3>Pending</h3>
        <span class="badge pending"><?php echo "<strong>PENDING</strong> - " . $pendingCount; ?></span>
        <p>Waiting for review</p>
    </div>

    <div class="status-card" onclick="filterStatus('Approved')">
        <h3>Approved</h3>
        <span class="badge approved"><?php echo "<strong>APPROVED</strong> - " . $approvedCount; ?></span>
        <p>Approved successfully</p>
    </div>

    <div class="status-card" onclick="filterStatus('Rejected')">
        <h3>Rejected</h3>
        <span class="badge rejected"><?php echo "<strong>REJECTED</strong> - " . $rejectCount; ?></span>
        <p>Insufficient details</p>
    </div>

    <div class="status-card" onclick="filterStatus('All')">
        <h3>All</h3>
        <span class="badge all"><?php echo "<strong>ALL</strong> - " . $allCount; ?></span>
        <p>All details</p>
    </div>
</div>

<script>
    function filterStatus(status) {
        window.location.href = '?status=' + status;
    }
</script>



<!-- ///End  on  Pending and Approved Rejected// -->


                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">User Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" method="POST">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name <span  style="color:red;">*</span></span></label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name.."required  oninput="validateName(this)" >
                                                <div id="nameError" class="text-danger" style="display:none">Only letters are allowed!. Special characters and numbers are not allowed!.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label"> Email <span style="color:red;">*</span></label>
                                                <input type="email" class="form-control"  id="email" name="email"  placeholder="Enter Email.."  required  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                  title="Please enter a valid email address..">
                                            </div>

                                            <div class="mb-3">
                                                <label for="roll" class="form-label">Roll <span  style="color:red;">*</span></span></label>
                                                <select name="roll" id="roll" class="form-select" required>
                                                    <option value="admin">Admin</option>
                                                    <option value="partner">Partner</option>
                                                </select>
                                            </div>
                                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

 <form action="" method="POST">
<div class="row mt-5 align-items-end">

  <div class="col-md-3">
    <!-- <label for="choose" class="form-label">Role</label> -->
    <select class="form-select" id="choose" name="choose" required>
      <option value="">Choose..</option>
      <option value="admin">Admin</option>
      <option value="partner">Partner</option>
     
    </select>
    <div class="invalid-feedback">
      Please select a valid option.
    </div>
  </div>


    <div class="col-md-3">
    <!-- <label for="choose_name" class="form-label text-center">Name</label> -->
    <input type="text" id="choose_name" name="choose_name" class="form-control" placeholder="Enter your Name...." required>
  </div>

  <div class="col-md-3">
    <!-- <label for="month" class="form-label">Month</label> -->
    <input type="month" id="month" name="month" class="form-control" required>
  </div>

 
  <div class="col-md-1">
    <button type="submit" id="search" name="search_btn" style="margin-left: -21px"; class="btn btn-primary w-10" >
      <i class="fas fa-search"></i> 
    </button>
  </div>
</button>
</div>
</form>



  <div class="card-body mt-4">
    
 <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                      
    <table class="table table-bordered">
        <thead class="text-center" style="background:black; color:white">
            <tr>
                <th>S.No</th>
                <th>EXPENSE ID</th>
                <th>NAME</th>
                <!-- <th>ROLE</th> -->
                <th>AMOUNT</th>
                <th>DATE</th>
                <th>CATEGORY</th>
                <th>DESCRIPTION</th>
                <th>INVOICE NUMBER</th>
                <th>ATTACHMENT</th>
                <th>STATUS</th>
                <th colspan="2">ACTION</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // include('db.php');
           
            $statusFilter = $_GET['status'] ?? 'All';
            $sql = "SELECT expense_entry.*, users.username FROM expense_entry JOIN users ON expense_entry.user_id = users.id";

            if ($statusFilter !== 'All') {
                $sql .= " WHERE expense_entry.status = '$statusFilter'";
            }

            $sql .= " ORDER BY expense_entry.id DESC";

            $run = mysqli_query($connection, $sql);
            $sno = 1;

            while ($row = mysqli_fetch_array($run)) {
                $id = $row['id'];
                $expense_id = $row['expense_id'];
                $username = $row['username'];

                // $role=$row['roll'];

                $amount = $row['amount'];
                $date = date("d-m-Y", strtotime($row['expense_date']));
                $category = $row['category'];
                $description = $row['description'];
                $admin_invoices_number=$row['invoice_number'];
                $filename = $row['filename'];
                $status = $row['status'];
               
               
            ?>
            <tr class="text-center">
                <td><?= $sno++; ?></td>
                <td><?= $expense_id; ?></td>
                <td><?= $username; ?></td>
                <!-- <td><?= $role?></td> -->
                <td><?= $amount; ?></td>
                <td><?= $date; ?></td>
                <td><?= $category; ?></td>
              
              
               
                <td style="max-width: 200px; word-break: break-word; overflow- Y: auto; overflow-y: hidden; white-space: nowrap;"><?php echo $description; ?>
                <td><?=  $admin_invoices_number; ?></td>
                <td>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#attachmentModal<?= $id ?>">
                    <i class="fa-solid fa-eye"></i> view
                    </button>

                    <!-- Attachment Modal -->
                    <div class="modal fade" id="attachmentModal<?= $id ?>" tabindex="-1" aria-labelledby="attachmentModalLabel<?= $id ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="text-primary">EXPENSE ID : <?= $expense_id ?></h5>&nbsp; &nbsp; 
                                <h5 class="text-info">NAME : <?= $username ?></h5>&nbsp; &nbsp; &nbsp; 
                                <h5 class="text-success">AMOUNT : <?= $amount ?></h5><br>&nbsp; &nbsp; 
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <?php
                                    if (!empty($filename)) {
                                        $files = explode(',', $filename);
                                        foreach ($files as $file) {
                                            echo "<img src='upload/{$file}' class='img-fluid mb-2' alt='Expense Attachment'><br>";
                                        }
                                    } else {
                                        echo "No attachment available.";
                                    }
                                    ?>
                                </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                </td>
               
            
               

                <td><?= $status; ?></td>

                <?php if ($status == 'Pending' && $statusFilter!='All'): ?>
                <td><a href="update_status.php?id=<?= $id ?>&status=Approved" class="btn btn-success">Approve</a></td>
                <td>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $id ?>">Reject</button>
                </td>
                <?php else: ?>
                <td colspan="2">—</td>
                <?php endif; ?>
            </tr>
          
             <!-- Approved Modal -->

             <!-- Approved Modal functionality was woking on update_satus.php/
        -->






            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal<?= $id ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $id ?>" aria-hidden="true">
                <div class="modal-dialog">
                <form action="reject.php" method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= $expense_id ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <textarea class="form-control" name="reject_reason" placeholder="Reason for rejection..." required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="reject" class="btn btn-danger">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php } ?>
        </tbody>
    </table>
</div>
</div>

        </div>
        
<!-- // Admin Expense Session Page Start /// -->

 <div id="expense" class="content-section" id="sample">

        <div class="container-fluid p-5 ">
        <div class="row">
            <div class="col-12">
        
                <div class="card mt-5">
                    <div class="card-header"  style=background:#0148B7;>
                        <h1 class="text-center text-white" >ADMIN EXPENSE</h1>
                    </div>
                    <div class="card-body">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal2">
                        <i class="fa-solid fa-plus"></i>

                        Add Expenses
                        </button>

<!-- Modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel2">Expense Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="#"  method="POST" enctype="multipart/form-data">
                          
                        <div class="mb-3">
                            <label for="invoivce_numbers" class="form-label">Invoice Number <span  style="color:red;">*</span></span></label>
                            <input type="text" class="form-control" id="invoice_numbers" name="invoice_numbers" placeholder="Enter the Invoice number or Transaction_id or N/A..." required  >
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
                        
                            <label for="category" class="form-label">Category <span  style="color:red;">*</span></span></label>
                            <select name="category" id="category" class="form-control"  onchange="toggleDescription()" required>
                            <option value="">--Select--</option>
                            <option value="food">Food</option>
                            <option value="travel">Travel</option>  
                            <option value="office_supplies">Office Supplies </option>
                            <option value="others">Others</option>  
                            
                            </select>
                            <div id="other-description" style="display:none; margin-top: 10px;">
                              <label for="other_description">Others Description :</label>
                              <textarea name="others_description" id="other_description" class="form-control" placeholder="Enter description.." style="resize: none;"></textarea>
                            </div>
                          
                            
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description<span  style="color:red;">*</span></span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter the Description.." style="resize: none;"></textarea>
                        </div>

                      
                        <div class="mb-3">
                            <label for="file">File <span style="color:red;">*</span></label>
                            <input type="file" class="form-control" id="file" name="file[]" multiple accept="image/jpeg, image/png, image/gif, image/jpg" onchange="validateFiles(this)">
                            <div class="invalid-feedback" id="file-error">Only images less than 2MB are allowed (JPEG, PNG, GIF). PDF, DOC, and HTML files are not allowed.</div>
                        </div>           
                        <button type="submit" name="submit2" class="btn btn-success" value="submit">Submit</button>

        </form>
      </div>
      
    </div>
  </div>
</div>


</div>

 <div class="card-body">
 <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">


<table class="table table-bordered border-dark">
  <!-- <thead class="text-center" style="background:#6C757D; color:white"> -->
  <thead class="text-center bg-secondary text-white">
    <tr>
                <th>S.No</th>
                <th>EXPENSE ID</th>
                <th>VOUCHER ID</th>
                <th>NAME</th>
                <!-- <th>ROLE</th> -->
                <th>AMOUNT</th>
                <th>DATE</th>
                <th>CATEGORY</th>
                <th>DESCRIPTION</th>
                <th>INVOICE NUMBER</th>
                <th>ATTACHMENT</th>
                <th>STATUS</th>
                <th colspan="2">VOUCHER</th>
    </tr>

  </thead>
  <tbody >
    <?php 
    
   
$sql = "SELECT expense_entry.*, users.username FROM expense_entry JOIN users ON expense_entry.user_id = users.id 
WHERE expense_entry.user_id = {$_SESSION['user_id']}";
      $run = mysqli_query($connection, $sql);
      $id=1;
      $status="Approved";
      while($row=mysqli_fetch_array($run)){
        $uid=$row['id'];
        $expenseId = $row['expense_id']; 
        $voucherId=$row['voucher_id'];
        $user_id = $row['user_id'];
        $username= $row['username'];
        $role="admin";
        $amount = $row['amount'];
        $date = $row['expense_date'];
        $formattedDate = date("d-m-Y", strtotime($date));
        $category = $row['category'];
        $description = $row['description'];
        $invoicenumber=$row['invoice_number'];
        $filename = $row['filename'];
        $modalId = "modal_" . $expenseId;

    
    
    ?>
  <tr class="text-center ">
      <td><?php echo $id ?></td>
      <td><?php echo $expenseId ?></td>
      <td><?php echo $voucherId ?></td>
      <!-- <td><?php echo $user_id ?></td> -->
      <td><?php echo $username ?></td>
      <!-- <td><?php echo $role ?></td> -->
      <td><?php echo $amount?></td>
      <td><?php echo $formattedDate ?></td>
      <td><?php echo $category?></td>
      <td style="max-width: 200px; word-break: break-word; overflow- Y: auto; overflow-y: hidden; white-space: nowrap;"><?php echo $description; ?></td>

      <td><?php  echo  $invoicenumber?></td>
      <td>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
        <i class="fa-regular fa-eye"></i> 
        </button>&nbsp;

        <!-- //Edit Button  Triger// -->
      <!-- <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $expenseId; ?>">
        <i class="fa-solid fa-pen-to-square text-white"></i>
      </button> -->

    <!-- //Edit Button Triger// -->
<!-- <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal_<?php echo $expenseId; ?>">
<i class="fa-solid fa-pen-to-square text-white"></i>
</button> -->


 <?php
// if($status_name != "Approved") {
// if($status== "Approved") {
//   echo '<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal_' .$expenseId . '">
//   <i class="fa-solid fa-pen-to-square text-white"></i>
//   </button>';
// }

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
        <input type="text" name="expense_id" value="<?php echo $expenseId; ?>">

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
          <input type="text" class="form-control" name="description" id="description_<?php echo $expenseId; ?>" value="<?php echo $description; ?>" required>
        </div>

        <div class="mb-3">
          <label for="file_<?php echo $expenseId; ?>" class="form-label d-flex">File<spam style="color:red;"> *</spam></label>
          <input type="file" class="form-control" name="file[]" id="file_<?php echo $expenseId; ?>" multiple>
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
      <div class="modal-dialog  modal-xl">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="<?php echo $modalId; ?>Label">Invoice  Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body text-center">
            <div class="d-flex flex-column ">
              <div class="start">
              <div class="start text-primary"><strong>Expense_ID :</strong> <?php echo $expenseId; ?></div>
              <div class="start text-info" ><strong>Username :</strong> <?php echo $username; ?></div>
              <!-- <div><strong>User ID :</strong> <?php echo $user_id; ?></div> -->
               <div  class="start text-success"><strong>Amount :</strong> <?php echo $amount; ?></div>
               </div>
              
              <?php if (!empty($filename)) {
              $files = explode(',', $filename);
              foreach ($files as $file) {
              ?>
               <div style="max-height: 400px; overflow: auto; border: 1px solid #ccc; padding: 10px;" class="modal-xl" >
                <img src="upload/<?php echo $file; ?>" class="img-fluid mt-2 zoom-image"  alt="Expense Image" style="width: ; height: ; object-fit: cover;">
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
  </td>
  <td><?php echo $status ?></td>
<td>

<?php
      // if( $status_name=="Pending"){
      //   // echo '<div class="alert alert-warning">Status is pending, so you can\'t download.</div>';
      //   echo '<button class="btn btn-primary" disabled><i class="bi bi-receipt"></i> Voucher</button>';
      //  }
      // elseif($status_name=="Approved"){
      //   // echo '<div class="alert alert-success">Successfully downloaded</div>';
      //   echo '<a href="voucher.php?expense_id=1" class="btn btn-primary" target="_blank">
      //   <i class="bi bi-receipt"></i> Voucher </a>';

      // }
 
      
      
    ?>
   
</td>

<tr>
  <?php
  
  }
  ?>
</tr>


</tr>
     
  </tbody>
</table>
</div>

</div>
                </div>
            </div>
        </div>
        <div></div>
    </div>
    <div>
     <!-- Pagination Links -->
<nav aria-label="Page navigation">
    <ul class="pagination d-flex justify-content-center">
        <!-- Previous Button -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="admin_dashbord.php?page=<?= $page - 1 ?>" <?= $page <= 1 ? 'aria-disabled="true"' : '' ?>>Previous</a>
        </li>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="admin_dashbord.php?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Next Button -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="admin_dashbord.php?page=<?= $page + 1 ?>" <?= $page >= $totalPages ? 'aria-disabled="true"' : '' ?>>Next</a>
        </li>
    </ul>
</nav>
 
</div>

  </div>



<!-- // Admin Expense Session Page End /// -->

        <!-- Users Section -->
        <div id="users" class="content-section">
        <div class="container-fluid p-5">
    <div class="row">
      <div class="col-12">
        <div class="card mt-5">
          <div class="card-header" style="background:#0148B7;color:white">
            <h1 class="text-center">  PARTNER LIST</h1>
          </div>
          <div class="card-body">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal1">
                <i class="fa-solid fa-plus"></i> Add Partner
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Partner Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form action="" method="POST">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name <span  style="color:red;">*</span></span></label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name.."required  oninput="validateName(this)" >
                                                <div id="nameError" class="text-danger" style="display:none">Only letters are allowed!. Special characters and numbers are not allowed!.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label"> Email <span style="color:red;">*</span></label>
                                                <input type="email" class="form-control"  id="email" name="email"  placeholder="Enter Email.."  required  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                  title="Please enter a valid email address..">
                                            </div>

                                            <div class="mb-3">
                                                <label for="roll" class="form-label">Roll <span  style="color:red;">*</span></span></label>
                                                <select name="roll" id="roll" class="form-select" required>
                                                <option value="">--Select--</option>

                                                    <option value="admin">Admin</option>
                                                    <option value="partner">Partner</option>
                                                </select>
                                            </div>
                                            <button type="submit" name="submit" class="btn btn-success">Submit</button>
                        </form>
                      </div>
                      
                      
                    </div>
                  </div>
                </div>
          </div>


    <div class="card-body ">
    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-bordered border-dark">
  <thead class="bg-secondary text-white text-center">
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">USERNAME</th>
      <th scope="col">EMAIL</th>
      <!-- <th scope="col">PASSWORD</th> -->
      <th scope="col">ROLE</th>
       <th scope="col">STATUS</th>
      <th colspan="2">ACTION</th>
    </tr>
  </thead>
  <tbody>
    <?php
          $sql="SELECT * FROM users";
          $run = mysqli_query($connection, $sql);
          $id=1;
          while($row=mysqli_fetch_array($run)){
            $uid=$row['id'];
            $usernames=$row['username'];
            $email=$row['email'];
            // $password=$row['password'];
            $role=$row['roll'];
            $ActiveSatus = $row['Action'];

    ?>
    <tr class="text-center">
      <td><?php  echo $id?></td>
      <td><?php  echo $usernames?></td>
      <td><?php  echo $email?></td>
      <!-- <td><?php  echo $password?></td> -->
      <td><?php  echo  $role?></td>
       <td><?php  echo   $ActiveSatus?></td>
      <td class="text-center">
       <div class="d-flex justify-content-center">

  <!-- Button trigger modal -->
  <button type="button" class="btn btn-warning text-white px-3 py-2 " data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>">
  Edit
  </button>

  <!-- Admin-partner-Edit-Modal -->
  <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Partner Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <form action="update_partner.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <div class="mb-3">
              <label for="userusername<?php echo $row['id']; ?>" class="form-label" style="margin-right: 87%;">Name <span style="color:red;">*</span></label>
              <input type="text" class="form-control" id="username<?php echo $row['id']; ?>" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" placeholder="Enter Name.." required >
              <div id="nameError" class="text-danger" style="display:none">Only letters are allowed!. Special characters and numbers are not allowed!.</div>
            </div>
            <div class="mb-3">
              <label for="email<?php echo $row['id']; ?>" class="form-label" style="margin-right: 87%;">Email <span style="color:red;">*</span></label>
              <input type="email" class="form-control" id="email<?php echo $row['id']; ?>" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" placeholder="Enter Email.." required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address..">
            </div>
            <div class="mb-3">
              <label for="roll<?php echo $row['id']; ?>" class="form-label" style="margin-right: 90%;">Roll <span style="color:red;">*</span></label>
              <select name="roll" id="roll<?php echo $row['id']; ?>" class="form-select" required>
                <option value="">--Select--</option>
                <option value="admin" <?php echo ($row['roll'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="partner" <?php echo ($row['roll'] == 'partner') ? 'selected' : ''; ?>>Partner</option>
              </select>
            </div>
            <button type="submit" name="btn-submit" class="btn btn-primary" style="margin-right: 83%;">Update</button>
          </form>

        </div>
      </div>
    </div>
  </div>
  <?php
$status = $row['Action'];
$btnClass = ($status == "Active") ? "btn btn-danger": "btn btn-success" ;
$btnText = ($status == "Active") ? "Deactivate" : "Activate";    
?>
</td>
<td>
  <form action="deactive.php" method="POST">
  <input type="hidden" name="id" value=<?php echo $row['id']; ?> >
  <button type="submit" class="<?= $btnClass ?>" name="deactive" value="<?= $status ?>">
        <?= $btnText ?>
    </button>
  </form>
  </div>
  </td>

 </tr>
  <?php
      $id++;
        }?>
        

  </tbody>
</table>
          </div>
          
        </div>
      </div>
    </div>
  </div>
        </div>
      </main>
    </div>
  </div>


  <!-- //Script// -->

  <script>
    function showSection(sectionId) {
      // Hide all content sections
      const sections = document.querySelectorAll(".content-section");
      sections.forEach((sec) => sec.classList.remove("active"));

      // Show selected section
      document.getElementById(sectionId).classList.add("active");


      // Highlight active sidebar link
      const links = document.querySelectorAll(".sidebar a");
      links.forEach(link => link.classList.remove("active"));
      event.target.classList.add("active");
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>