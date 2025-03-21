<?php
include 'config/db.php';

$id = $_GET['id'];
$sql = "DELETE FROM manatees WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
}
?>
