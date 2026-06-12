<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csvFile = 'attendance_records.csv';
    
    try {
        $currentDate = date('Y-m-d');
        
        if (file_exists($csvFile)) {
            // Read existing records
            $existingRecords = [];
            $handle = fopen($csvFile, 'r');
            if ($handle) {
                // Keep header row
                $headers = fgetcsv($handle);
                $existingRecords[] = $headers;
                
                // Keep records from other dates, remove today's records
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if (count($data) >= 4 && $data[3] !== $currentDate) {
                        $existingRecords[] = $data;
                    }
                }
                fclose($handle);
            }
            
            // Write back the filtered records
            $handle = fopen($csvFile, 'w');
            if ($handle) {
                foreach ($existingRecords as $record) {
                    fputcsv($handle, $record);
                }
                fclose($handle);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Today\'s records cleared successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error clearing records: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
