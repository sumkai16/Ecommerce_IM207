<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Aries\MiniFrameworkStore\Models\User;

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $birthdate = $_POST['birthdate'] ?? null;

    // Update user details in the database
    $userModel = new User();
    $userModel->update([
        'id' => $_SESSION['user']['id'],
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'phone' => $phone,
        'birthdate' => Carbon\Carbon::createFromFormat('Y-m-d', $birthdate)->format('Y-m-d')
    ]);

    // Update session data
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['address'] = $address;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['birthdate'] = $birthdate;

    echo "<script>alert('Account details updated successfully!');</script>";
}

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <h1>My Account</h1>
            <p>Welcome, <?php echo $_SESSION['user']['name']; ?></p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        <div class="col-md-8 bg-white p-5">
            <h2>Edit Account Details</h2>
            <form action="my-account.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $_SESSION['user']['name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['user']['email']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $_SESSION['user']['address'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $_SESSION['user']['phone'] ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="birthdate" class="form-label">Birthdate</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $_SESSION['user']['birthdate'] ?? ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary" name="submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php template('footer.php'); ?>