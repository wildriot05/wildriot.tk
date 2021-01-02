<?php
session_start();

// initializing variables
$username = "";
$email    = "";
$name     = "";
$address  = "";
$gender   = "";
$phno     = "";
$ano      = "";
$pno      = "";
$accno    = "";
$errors = array(); 

// connect to the database
$db = mysqli_connect('localhost', 'root', '', 'registration');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $address = mysqli_real_escape_string($db, $_POST['address']);
  $gender = mysqli_real_escape_string($db, $_POST['gender']);
  $phno = mysqli_real_escape_string($db, $_POST['phno']);
  $ano = mysqli_real_escape_string($db, $_POST['ano']);
  $pno = mysqli_real_escape_string($db, $_POST['pno']);
  $accno = mysqli_real_escape_string($db, $_POST['accno']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure 
  // a user does not already exist with the same username and/or email
  $user_check_query = "SELECT * FROM cli WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO cli (username, email, password, Name, Address, GENDER, PhoneNumber, AadharNumber, PANNumber, AccountNumber) 
  			  VALUES('$username', '$email', '$password', '$name', '$address', '$gender', '$phno', '$ano', '$pno', '$accno')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
	$_SESSION['name'] = $name;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index3.php');
  }
}

if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM cli WHERE username='$username' AND password='$password'";
	$records = mysqli_query($db,"select * from cli where username='$username'");
	$data = mysqli_fetch_array($records);
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
	  $_SESSION['name'] = $data['Name'];
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: index3.php');
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

?>