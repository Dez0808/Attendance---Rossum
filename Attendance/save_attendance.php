<?php
// save_attendance.php - Enhanced version with better validation and daily duplicate prevention

// Set proper headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

date_default_timezone_set('Asia/Manila');

try {
    // Get and validate POST data
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception('No data received');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $requiredFields = ['studentName', 'status', 'time', 'studentId'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("Missing or empty field: {$field}");
        }
    }

    $studentName = trim($data['studentName']);
    $status = strtoupper(trim($data['status']));
    $time = trim($data['time']);
    $studentId = trim($data['studentId']);

    // Validate status
    $validStatuses = ['PRESENT', 'LATE', 'ABSENT'];
    if (!in_array($status, $validStatuses)) {
        throw new Exception('Invalid status. Must be PRESENT, LATE, or ABSENT');
    }

    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](\s?(AM|PM))?$/i', $time)) {
        throw new Exception('Invalid time format. Must be HH:MM or HH:MM AM/PM');
    }

    // Sanitize inputs
    $studentName = preg_replace('/[^a-zA-Z0-9\\s\\.,\\-]/', '', $studentName);
    $studentId = preg_replace('/[^a-zA-Z0-9]/', '', $studentId);
    
    if (empty($studentName) || empty($studentId)) {
        throw new Exception('Invalid student data after sanitization');
    }

    $csvFile = 'attendance_records.csv';
    $currentDate = date('Y-m-d');
    $currentDateTime = date('Y-m-d H:i:s');
    
    if (file_exists($csvFile)) {
        $handle = fopen($csvFile, 'r');
        if ($handle) {
            // Skip header row
            fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== FALSE) {
                // Check if student already recorded today
                if (count($row) >= 5 && $row[5] === $studentId && $row[3] === $currentDate) {
                    fclose($handle);
                    throw new Exception('Student has already been recorded today');
                }
            }
            fclose($handle);
        }
    }
    
    // Create CSV headers if file doesn't exist
    if (!file_exists($csvFile)) {
        $headers = ['Student Name', 'Status', 'Time', 'Date', 'Timestamp', 'Student ID'];
        $fp = fopen($csvFile, 'w');
        if (!$fp) {
            throw new Exception('Cannot create CSV file');
        }
        fputcsv($fp, $headers);
        fclose($fp);
    }

    // Append attendance record
    $record = [$studentName, $status, $time, $currentDate, $currentDateTime, $studentId];
    
    $fp = fopen($csvFile, 'a');
    if (!$fp) {
        throw new Exception('Cannot open CSV file for writing');
    }

    // Lock file to prevent concurrent writes
    if (flock($fp, LOCK_EX)) {
        if (fputcsv($fp, $record) === false) {
            flock($fp, LOCK_UN);
            fclose($fp);
            throw new Exception('Failed to write to CSV file');
        }
        flock($fp, LOCK_UN);
    } else {
        fclose($fp);
        throw new Exception('Cannot lock CSV file');
    }
    
    fclose($fp);

    // Log successful save
    error_log("Attendance saved: {$studentName} ({$studentId}) - {$status} at {$time}");

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Attendance recorded successfully',
        'data' => [
            'student' => $studentName,
            'studentId' => $studentId,
            'status' => $status,
            'time' => $time,
            'date' => $currentDate
        ]
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Attendance save error: " . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
