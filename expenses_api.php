<?php
require_once 'db_connection.php';

// GET all expenses
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM expenses";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $expenses = array();
        while ($row = $result->fetch_assoc()) {
            $expenses[] = $row;
        }
        echo json_encode($expenses);
    } else {
        echo "No expenses found.";
    }
}

// POST (add) an expense
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $amount = $_POST['amount'];

    $sql = "INSERT INTO expenses (name, amount) VALUES ('$name', '$amount')";
    if ($conn->query($sql) === TRUE) {
        echo "Expense added successfully.";
    } else {
        echo "Error adding expense: " . $conn->error;
    }
}

// PUT (edit) an expense
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $put_vars);
    $expenseId = $put_vars['id'];
    $name = $put_vars['name'];
    $amount = $put_vars['amount'];

    $sql = "UPDATE expenses SET name='$name', amount='$amount' WHERE id='$expenseId'";
    if ($conn->query($sql) === TRUE) {
        echo "Expense updated successfully.";
    } else {
        echo "Error updating expense: " . $conn->error;
    }
}

// DELETE an expense
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $delete_vars);
    $expenseId = $delete_vars['id'];

    $sql = "DELETE FROM expenses WHERE id='$expenseId'";
    if ($conn->query($sql) === TRUE) {
        echo "Expense deleted successfully.";
    } else {
        echo "Error deleting expense: " . $conn->error;
    }
}

$conn->close();
?>
