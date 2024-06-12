<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../home.php");
    exit();
}

if (isset($_POST['name'], $_POST['amount'], $_POST['date'], $_POST['type'])) {
    $userId = $_SESSION['id'];
    $type = $_POST['type'] == 'on' ? 'Income' : 'Expense';
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    $stmt = $con->prepare("INSERT INTO transactions (UserId, Type, Name, Amount, Date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $userId, $type, $name, $amount, $date);

    if ($stmt->execute()) {
        header("Location: ../home.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
} else {
    echo "Invalid input.";
}
?>
