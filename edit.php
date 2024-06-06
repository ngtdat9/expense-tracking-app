<?php
session_start();
include("php/config.php");

// Ensure the session ID is set
if (!isset($_SESSION['id'])) {
    die("No session ID found.");
}

$id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/editstyle.css">
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php"> Logo</a></p>
        </div>

        <div class="right-links">
            <a href="home.php">Change Profile</a>
            <a href="php/logout.php"><button class="btn">Log Out</button></a>
        </div>
    </div>

    <div class="container">
        <div class="box form-box">
            <?php
            if (isset($_POST['submit'])) {
                $username = $_POST['username'];
                $email = $_POST['email'];

                // Check if fields are empty
                if (empty($username) || empty($email)) {
                    die("Username or Email cannot be empty.");
                }

                // Use prepared statements to prevent SQL injection
                $stmt = mysqli_prepare($con, "UPDATE users SET Username=?, Email=? WHERE Id=?");
                mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);

                if (mysqli_stmt_execute($stmt)) {
                    // Update the session username
                    $_SESSION['username'] = $username;
                    
                    echo "<div class='message'>
                            <p>Profile Updated!</p>
                          </div> <br>";
                    echo "<a href='home.php'><button class='btn'>Go Home</button>";
                } else {
                    die("Error updating profile: " . mysqli_stmt_error($stmt));
                }

                mysqli_stmt_close($stmt);
            } else {
                // Fetch the current user data
                $stmt = mysqli_prepare($con, "SELECT Username, Email FROM users WHERE Id=?");
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $res_Uname, $res_Email);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }
            ?>
            <header>Change Profile</header>
            <form action="edit.php" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($res_Uname); ?>" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($res_Email); ?>" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Update" required>
                </div>
            </form>
        </div>
    </div>
</body>
</html>