

<?php
session_start();
include('db.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $md5password = md5($password);  // Match with DB hash

    // Check if email exists
    $checkEmailQuery = "SELECT * FROM users WHERE email='$email'";
    $emailResult = mysqli_query($connection, $checkEmailQuery);

    if (mysqli_num_rows($emailResult) == 0) {
        $error = "Email does not exist.";
    } else {
        $user = mysqli_fetch_assoc($emailResult);
        
        // Check if account is active
        if ($user['Action'] == 'Deactivate') {  // Assuming 'Action' column stores status
            $error = "Your account is deactivated. Please contact admin.";
        } else {
            // Now check password match
            $loginQuery = "SELECT * FROM users WHERE email='$email' AND password='$md5password'";
            $loginResult = mysqli_query($connection, $loginQuery);

            if (mysqli_num_rows($loginResult) == 1) {
                $row = mysqli_fetch_assoc($loginResult);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['roll'] = $row['roll'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['password'] = $row['password']; // optional

                if ($row['roll'] == "admin") {
                    header("Location: admin_dashbord.php");
                    exit;
                } elseif ($row['roll'] == "partner") {
                    header("Location: partner_dashboard.php");
                    exit;
                }
            } else {
                $error = "Invalid password.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

    <div class="container">
      
        <div class="row justify-content-center">
          
            <div class="col-7 mt-5">
            <div class="imgcontainer">
    <img src="img/logo.png" alt="Avatar" class="avatar" style="
    margin-left: 29%;">
  </div>
                <div class="card mt-5">
        
                    <div class="card-header" style="background:green;color:white">
                        <h1 class="text-center">LOGIN</h1>
                    </div>
                    <div class="card-body">
                        <?php  if(isset($error)):?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?= $error ?>
                        </div>
                        <?php endif; ?>

  
<form action=""method="POST">
  
<div class="container mt-4">
  

  
    <div class="row g-2 align-items-center mb-3">
      
      <div class="col-auto">
        <label for="email" class="col-form-label">
      
          <i class="fa-solid fa-envelope me-" style="color:green;"></i>&nbsp; EMAIL<span style="color:red;">*</span>  
        </label>
      </div>
      <div class="col-9">
        <input type="email" name="email" id="email" class="form-control" style="margin-left: 12%;"  placeholder="Enter the Email..." required>
      </div>
    </div>

 
    <div class="row g-2 align-items-center mb-3">
  <div class="col-auto">
    <label for="password" class="col-form-label">
      <i class="fas fa-lock me-1" style="color:green;"  ></i> PASSWORD <span style="color:red;">*</span>
    </label>
  </div>
  <div class="col-9">
    <input type="password" name="password" id="password" class="form-control" placeholder="Enter the password..." required>
  </div>
</div>

 <div class="text-center">
  <button type="submit" class="btn btn-success w-50" name="login" value="login">
    <i class="fas fa-sign-in-alt me-2"></i> Login
  </button>
</div>
</div>

</form>
<div>
<a href="forget_password.php">Forget Password ?</a>
</div>

                        

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
      
    </script>
</body>
</html>