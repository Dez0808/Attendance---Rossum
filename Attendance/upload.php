<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'attendance_system';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]));
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $s_id = $_POST["student_id"];
    $name = $_POST["s_name"];
    $lrn = $_POST["s_lrn"];
    $gender = $_POST["s_gender"];

    $sql = "INSERT INTO students (student_id, name, lrn, gender) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $s_id, $name, $lrn, $gender);

    if ($stmt->execute()) {
        echo "<script>alert('Succesful'); window.location.href='add_students.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
