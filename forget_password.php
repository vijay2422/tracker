<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/forget_password.css">
   
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-10">
                <div class="card mt-5 mb-5">
                    <div class="card-header bg-primary text-white">
                        <h1 class="text-center m-0 py-2">FORGET PASSWORD</h1>
                    </div>
                    <div class="card-body p-4">
                        <form action="send_reset.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address :</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg" name="send">Send</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>