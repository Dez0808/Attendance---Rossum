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

$conn->set_charset("utf8mb4");

function getAllStudents(mysqli $conn) {
    $students = [];
    $query = "SELECT student_id, name, lrn, gender FROM students ORDER BY CAST(SUBSTR(student_id, 2) AS UNSIGNED)";
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'id' => $row['student_id'],
                'name' => $row['name'],
                'lrn' => $row['lrn'],
                'gender' => $row['gender']
            ];
        }
    }
    
    return $students;
}

function getStudentsArray(mysqli $conn) {
    $students = [];
    $query = "SELECT student_id, name, gender FROM students ORDER BY CAST(SUBSTR(student_id, 2) AS UNSIGNED)";
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[$row['student_id']] = [
                'name' => $row['name'],
                'gender' => $row['gender']
            ];
        }
    }
    
    return $students;
}
?>
