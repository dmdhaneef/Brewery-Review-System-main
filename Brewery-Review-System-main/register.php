<?php

include 'components/connect.php';

if(isset($_POST['submit'])){

   $id = create_unique_id();
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $c_pass = password_verify($_POST['c_pass'], $pass);
   $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = create_unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
         $warning_msg[] = 'Image size is too large!';
      }else{
         move_uploaded_file($image_tmp_name, $image_folder);
      }
   }else{
      $rename = '';
   }

   $verify_email = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $verify_email->execute([$email]);

   if($verify_email->rowCount() > 0){
      $warning_msg[] = 'Email already taken!';
   }else{
      if($c_pass == 1){
         $insert_user = $conn->prepare("INSERT INTO `users`(id, name, email, password, image) VALUES(?,?,?,?,?)");
         $insert_user->execute([$id, $name, $email, $pass, $rename]);
         $success_msg[] = 'Registered successfully!';
      }else{
         $warning_msg[] = 'Confirm password not matched!';
      }
   }
   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/header.php'; ?>
<!-- header section ends -->

<section class="account-form">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Create Your Account!</h3>
      <p class="placeholder">Enter your Name <span>*</span></p>
      <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="box">
      <p class="placeholder">Enter your Email <span>*</span></p>
      <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="box">
      <p class="placeholder">Enter your Password <span>*</span></p>
      <input type="password" name="pass" required maxlength="50" placeholder="Enter your password" class="box">
      <p class="placeholder">Confirm your Password <span>*</span></p>
      <input type="password" name="c_pass" required maxlength="50" placeholder="Confirm your password" class="box">
      <p class="placeholder">Upload Profile</p>
      <input type="file" name="image" class="box" accept="image/*">
      <p class="link">Already have an account? <a href="login.php">Login</a></p>
      <input type="submit" value="register now" name="submit" class="btn">
   </form>

</section>














<!-- sweetalert cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/alers.php'; ?>

</body>
</html>