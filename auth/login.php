<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
include '../helpers/functions.php';

use Aries\MiniFrameworkStore\Models\User;

$user = new User();

if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header('Location: ../main/index.php');
    exit;
}

if(isset($_POST['submit'])) {
    $user_info = $user->login([
        'email' => $_POST['email'],
    ]);

    if($user_info && password_verify($_POST['password'], $user_info['password'])) {
        $_SESSION['user'] = $user_info;
        header('Location: ../main/index.php');
        exit;
    } else {
        $message = 'Invalid username or password';
    }
}
?>

<?php template('header.php'); ?>


<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
        <h1 class="text-center mb-3">Login</h1>
        <h3 class="text-center text-danger mb-3"><?php echo isset($message) ? $message : ''; ?></h3>
        <form action="login.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                <div class="invalid-feedback">Please enter a valid email address.</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1" required>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Remember me</label>
            </div>
            <button type="submit" name="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="register.php" class="btn btn-link">Register here</a></p>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>
