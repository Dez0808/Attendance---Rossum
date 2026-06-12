<?php

date_default_timezone_set('Asia/Manila');

$csvFile = 'attendance_records.csv';
$records = [];
$students = [];

function convertTo12Hour($time24) {
    if ($time24 === 'Auto-marked' || $time24 === '-' || empty($time24)) {
        return $time24;
    }
    
    // Check if time already has AM/PM
    if (preg_match('/(AM|PM|am|pm)$/i', $time24)) {
        return $time24; // Already in 12-hour format, return as-is
    }
    
    // Handle both HH:MM and HH:MM:SS formats
    $timeParts = explode(':', $time24);
    if (count($timeParts) < 2) {
        return $time24;
    }
    
    $hour = (int)$timeParts[0];
    $minute = $timeParts[1];
    
    $ampm = $hour >= 12 ? 'PM' : 'AM';
    $hour12 = $hour % 12;
    if ($hour12 === 0) $hour12 = 12;
    
    return sprintf('%d:%s %s', $hour12, $minute, $ampm);
}

// Load student data for Excel-like view
$studentList = [
    "S001" => ["name" => "ALBANIA, JUSTINE JOHN L.", "gender" => "male"],
    "S002" => ["name" => "ALEGRE, SANDRO C.", "gender" => "male"],
    "S003" => ["name" => "ARGUILLES, CYRUZ RENZ E.", "gender" => "male"],
    "S004" => ["name" => "BALUYA, DANNE EARLL GABBRIEL A.", "gender" => "male"],
    "S005" => ["name" => "BERMUDEZ, DEZCARTES REY F.", "gender" => "male"],
    "S006" => ["name" => "CABUSAO, RAFAEL S.", "gender" => "male"],
    "S007" => ["name" => "CAPILI, ALLEN GABRIEL L.", "gender" => "male"],
    "S008" => ["name" => "DACARA, PRINCE GHAVRIEL P.", "gender" => "male"],
    "S009" => ["name" => "ELCANO, KELVIN T.", "gender" => "male"],
    "S010" => ["name" => "GAMBOT, JOEFFER REX S.", "gender" => "male"],
    "S011" => ["name" => "GARCIA, JOHN LEMAR C.", "gender" => "male"],
    "S012" => ["name" => "HIPOL, JOHAN ALEXIS D.", "gender" => "male"],
    "S013" => ["name" => "JUNSAY, KIERAN SHIN N.", "gender" => "male"],
    "S014" => ["name" => "LAZO, JANNIEL GILL O.", "gender" => "male"],
    "S015" => ["name" => "MANGENTE, ELIJAH GWAIN C.", "gender" => "male"],
    "S016" => ["name" => "MARQUEZ, KARL S.", "gender" => "male"],
    "S017" => ["name" => "MONTENEGRO, JOSHUIA V.", "gender" => "male"],
    "S018" => ["name" => "PEREZ, JOHN CHRISTIAN B.", "gender" => "male"],
    "S019" => ["name" => "PILI, REXEL LUTHER T.", "gender" => "male"],
    "S020" => ["name" => "SANTOS, ANDREI B.", "gender" => "male"],
    "S021" => ["name" => "SUGUITAN, JOHN MICHAEL S.", "gender" => "male"],
    "S022" => ["name" => "TECSON, JOSE MARI C.", "gender" => "male"],
    "S023" => ["name" => "TORRES, ETHAN BEIGE D.", "gender" => "male"],
    "S024" => ["name" => "TUASON, ADRIAN KELLY T.", "gender" => "male"],
    "S025" => ["name" => "VILLAMOR, EARL STEPHEN P.", "gender" => "male"],
    "S026" => ["name" => "ABALOYAN, MARIA ZHULLIANNE M.", "gender" => "female"],
    "S027" => ["name" => "BARBADILLO, ANN KIRSTINE A.", "gender" => "female"],
    "S028" => ["name" => "BOSH, FRANZENEL G.", "gender" => "female"],
    "S029" => ["name" => "DUMO, WELLA NICOLE SAY D.", "gender" => "female"],
    "S030" => ["name" => "FACUNLA, ARIANNEY MAE F.", "gender" => "female"],
    "S031" => ["name" => "JACOB, JASMIN S.", "gender" => "female"],
    "S032" => ["name" => "MENDOZA, ASHLY P.", "gender" => "female"],
    "S033" => ["name" => "PALAMING, MYLES M.", "gender" => "female"],
    "S034" => ["name" => "SALAZAR, ELIJAHGEN B.", "gender" => "female"],
    "S035" => ["name" => "SAN JUAN, DOMINIQUE KYLA YHANELLE A.", "gender" => "female"]
];

$attendanceData = [];
$dates = [];

if (file_exists($csvFile)) {
    $handle = fopen($csvFile, 'r');
    if ($handle) {
        // Skip header row
        $headers = fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) >= 6) {
                $studentName = $data[0];
                $status = $data[1];
                $time = $data[2];
                $date = $data[3];
                $studentId = $data[5];
                
                // Track unique dates
                if (!in_array($date, $dates)) {
                    $dates[] = $date;
                }
                
                // Organize by student ID and date
                if (!isset($attendanceData[$studentId])) {
                    $attendanceData[$studentId] = [
                        'name' => $studentName,
                        'records' => []
                    ];
                }
                
                $attendanceData[$studentId]['records'][$date] = [
                    'status' => $status,
                    'time' => $time
                ];
            }
        }
        fclose($handle);
    }
}

// Sort dates chronologically
sort($dates);

$currentDate = date('Y-m-d');
$currentTime = new DateTime();

$cutoffTime = new DateTime();
$cutoffTime->setTime(15, 0, 0); // 3:00 PM

$stats = ['PRESENT' => 0, 'LATE' => 0, 'ABSENT' => 0];
$genderStats = ['boys_present' => 0, 'girls_present' => 0, 'boys_total' => 25, 'girls_total' => 10];
$todayRecords = 0;

$studentsToAutoMark = [];

function getWeekdayDates($numDays = 5) {
    $dates = [];
    $currentDate = new DateTime();
    $daysAdded = 0;
    $daysBack = 0;
    
    while ($daysAdded < $numDays && $daysBack < 14) { // Limit search to 2 weeks back
        $checkDate = clone $currentDate;
        $checkDate->sub(new DateInterval("P{$daysBack}D"));
        
        // Check if it's a weekday (Monday = 1, Friday = 5)
        if ($checkDate->format('N') >= 1 && $checkDate->format('N') <= 5) {
            $dates[] = $checkDate->format('Y-m-d');
            $daysAdded++;
        }
        
        $daysBack++;
    }
    
    return array_reverse($dates); // Show oldest to newest
}

$weekdayDates = getWeekdayDates(5);

// Filter existing dates to only include weekdays
$filteredDates = [];
foreach ($dates as $date) {
    $dateObj = new DateTime($date);
    // Only include weekdays (Monday = 1, Friday = 5)
    if ($dateObj->format('N') >= 1 && $dateObj->format('N') <= 5) {
        $filteredDates[] = $date;
    }
}

// Merge and get unique weekday dates, limit to last 5 weekdays
$allWeekdayDates = array_unique(array_merge($filteredDates, $weekdayDates));
sort($allWeekdayDates);
$displayDates = array_slice($allWeekdayDates, -5); // Get last 5 weekdays

$pastDates = array_filter($displayDates, function($date) use ($currentDate) {
    return $date < $currentDate;
});

foreach ($pastDates as $pastDate) {
    foreach ($studentList as $studentId => $studentData) {
        // Check if student has no record for this past date
        if (!isset($attendanceData[$studentId]['records'][$pastDate])) {
            // Add auto-marked absent for past dates
            if (!isset($attendanceData[$studentId])) {
                $attendanceData[$studentId] = [
                    'name' => $studentData['name'],
                    'records' => []
                ];
            }
            
            $attendanceData[$studentId]['records'][$pastDate] = [
                'status' => 'ABSENT',
                'time' => 'Auto-marked'
            ];
        }
    }
}

// Process each student for today's attendance
foreach ($studentList as $studentId => $studentData) {
    $hasScannedToday = false;
    
    if (isset($attendanceData[$studentId]['records'][$currentDate])) {
        $record = $attendanceData[$studentId]['records'][$currentDate];
        if (isset($stats[$record['status']])) {
            $stats[$record['status']]++;
            $todayRecords++;
            $hasScannedToday = true;
            
            if ($record['status'] === 'PRESENT' || $record['status'] === 'LATE') {
                if ($studentData['gender'] === 'male') {
                    $genderStats['boys_present']++;
                } else {
                    $genderStats['girls_present']++;
                }
            }
        }
    }
    
    if (!$hasScannedToday && $currentTime > $cutoffTime) {
        $stats['ABSENT']++;
        $todayRecords++;
        
        // Add absent record to display data
        if (!isset($attendanceData[$studentId])) {
            $attendanceData[$studentId] = [
                'name' => $studentData['name'],
                'records' => []
            ];
        }
        
        $attendanceData[$studentId]['records'][$currentDate] = [
            'status' => 'ABSENT',
            'time' => 'Auto-marked'
        ];
        
        $studentsToAutoMark[] = [
            'id' => $studentId,
            'name' => $studentData['name']
        ];
    }
}

if (!empty($studentsToAutoMark) && file_exists($csvFile)) {
    $handle = fopen($csvFile, 'a'); // Open in append mode
    if ($handle) {
        foreach ($studentsToAutoMark as $student) {
            $row = [
                $student['name'],
                'ABSENT',
                'Auto-marked',
                $currentDate,
                date('H:i:s'),
                $student['id']
            ];
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}

// Ensure current date is in dates array for display
if (!in_array($currentDate, $displayDates)) {
    $displayDates[] = $currentDate;
    sort($displayDates);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records - Excel View</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .container {
            max-width: 100%;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #2c3e50, #34495e);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .header h1 i {
            margin-right: 0.5rem;
            color: #3498db;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .controls {
            padding: 1rem;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn i {
            margin-right: 0.4rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .btn-success {
            background: linear-gradient(45deg, #27ae60, #229954);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.8rem;
            padding: 1rem;
            background: #ecf0f1;
        }

        .stat-card {
            background: white;
            padding: 0.8rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .stat-number.present { color: #27ae60; }
        .stat-number.late { color: #f39c12; }
        .stat-number.absent { color: #e74c3c; }
        .stat-number.total { color: #3498db; }
        .stat-number.boys-present { color: #3498db; }
        .stat-number.girls-present { color: #e91e63; }

        .gender-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.8rem;
            padding: 1rem;
            background: #f8f9fa;
            margin-bottom: 1rem;
        }

        .gender-stat-card {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }

        .gender-stat-card.boys {
            border-left-color: #3498db;
            background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
        }

        .gender-stat-card.girls {
            border-left-color: #e91e63;
            background: linear-gradient(135deg, #fce4ec 0%, #ffffff 100%);
        }

        .gender-stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .gender-stat-card.boys .gender-stat-number {
            color: #3498db;
        }

        .gender-stat-card.girls .gender-stat-number {
            color: #e91e63;
        }

        .gender-stat-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #7f8c8d;
        }

        .gender-stat-label i {
            margin-right: 0.3rem;
        }

        .excel-table-container {
            padding: 1rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .excel-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 600px;
        }

        .excel-table th,
        .excel-table td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: center;
            font-size: 0.8rem;
        }

        .excel-table th {
            background: #34495e;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .excel-table th.student-header {
            background: #2c3e50;
            text-align: left;
            min-width: 180px;
            max-width: 180px;
        }

        .excel-table td.student-name {
            text-align: left;
            font-weight: 600;
            background: #f8f9fa;
            min-width: 180px;
            max-width: 180px;
            word-wrap: break-word;
            font-size: 0.75rem;
        }

        .excel-table th.date-header {
            min-width: 100px;
            writing-mode: horizontal-tb;
        }

        .status-cell {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            padding: 0.3rem;
            border-radius: 4px;
        }

        .status-present {
            background: #d5f4e6;
            color: #27ae60;
        }

        .status-late {
            background: #fef9e7;
            color: #f39c12;
        }

        .status-absent {
            background: #fadbd8;
            color: #e74c3c;
        }

        .status-not-scanned {
            background: #f8f9fa;
            color: #95a5a6;
        }

        .time-info {
            font-size: 0.6rem;
            opacity: 0.8;
            display: block;
            margin-top: 2px;
        }

        .no-records {
            text-align: center;
            padding: 2rem;
            color: #7f8c8d;
        }

        .no-records i {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            body {
                padding: 0.25rem;
            }

            .header {
                padding: 1rem;
            }

            .header h1 {
                font-size: 1.5rem;
                flex-direction: column;
                gap: 0.3rem;
            }

            .header p {
                font-size: 0.9rem;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
                padding: 0.8rem;
            }

            .btn {
                justify-content: center;
                padding: 0.8rem;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
                padding: 0.8rem;
            }

            .stat-card {
                padding: 0.6rem;
            }

            .stat-number {
                font-size: 1.3rem;
            }

            .gender-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
                padding: 0.8rem;
            }

            .gender-stat-card {
                padding: 0.6rem;
            }

            .gender-stat-number {
                font-size: 1.3rem;
            }

            .excel-table-container {
                padding: 0.5rem;
            }

            .excel-table {
                min-width: 500px;
            }

            .excel-table th.student-header,
            .excel-table td.student-name {
                min-width: 150px;
                max-width: 150px;
                font-size: 0.7rem;
            }

            .excel-table th,
            .excel-table td {
                padding: 0.4rem 0.2rem;
                font-size: 0.7rem;
            }

            .excel-table th.date-header {
                min-width: 80px;
                font-size: 0.65rem;
            }

            .status-cell {
                font-size: 0.65rem;
                padding: 0.2rem;
            }

            .time-info {
                font-size: 0.55rem;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.3rem;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }

            .gender-stats {
                grid-template-columns: 1fr 1fr;
            }

            .excel-table {
                min-width: 400px;
            }

            .excel-table th.student-header,
            .excel-table td.student-name {
                min-width: 120px;
                max-width: 120px;
                font-size: 0.65rem;
            }

            .excel-table th.date-header {
                min-width: 70px;
                font-size: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table"></i>Attendance Records</h1>
        </div>

        <div class="controls">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>Back to Scanner
            </a>
            <button onclick="exportToCSV()" class="btn btn-success">
                <i class="fas fa-download"></i>Export to CSV
            </button>
        </div>

        <?php if (!empty($attendanceData)): ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number present"><?php echo $stats['PRESENT']; ?></div>
                    <div>Present Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number late"><?php echo $stats['LATE']; ?></div>
                    <div>Late Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number absent"><?php echo $stats['ABSENT']; ?></div>
                    <div>Absent Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number total"><?php echo $todayRecords; ?></div>
                    <div>Today's Total</div>
                </div>
            </div>

            <!-- Added gender statistics -->
            <div class="gender-stats">
                <div class="gender-stat-card boys">
                    <div class="gender-stat-number"><?php echo $genderStats['boys_present']; ?>/<?php echo $genderStats['boys_total']; ?></div>
                    <div class="gender-stat-label">
                        <i class="fas fa-mars"></i>Boys Present
                    </div>
                </div>
                <div class="gender-stat-card girls">
                    <div class="gender-stat-number"><?php echo $genderStats['girls_present']; ?>/<?php echo $genderStats['girls_total']; ?></div>
                    <div class="gender-stat-label">
                        <i class="fas fa-venus"></i>Girls Present
                    </div>
                </div>
            </div>

            <div class="excel-table-container">
                <table class="excel-table" id="attendanceTable">
                    <thead>
                        <tr>
                            <th class="student-header">
                                <i class="fas fa-user"></i> Student Name
                            </th>
                            <?php foreach ($displayDates as $date): ?>
                                <th class="date-header">
                                    <i class="fas fa-calendar"></i><br>
                                    <?php 
                                    $dateObj = new DateTime($date);
                                    echo $dateObj->format('D'); // Show day name (Mon, Tue, etc.)
                                    ?><br>
                                    <?php echo date('M j', strtotime($date)); ?><br>
                                    <small><?php echo date('Y', strtotime($date)); ?></small>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentList as $studentId => $studentData): ?>
                            <tr class="<?php echo $studentData['gender']; ?>">
                                <td class="student-name">
                                    <strong><?php echo $studentId; ?></strong><br>
                                    <?php echo htmlspecialchars($studentData['name']); ?>
                                </td>
                                <?php foreach ($displayDates as $date): ?>
                                    <td>
                                        <?php if (isset($attendanceData[$studentId]['records'][$date])): ?>
                                            <?php 
                                            $record = $attendanceData[$studentId]['records'][$date];
                                            $statusClass = 'status-' . strtolower($record['status']);
                                            $displayTime = convertTo12Hour($record['time']);
                                            ?>
                                            <div class="status-cell <?php echo $statusClass; ?>">
                                                <?php echo $record['status']; ?>
                                                <span class="time-info"><?php echo $displayTime; ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="status-cell status-not-scanned">
                                                -
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-records">
                <i class="fas fa-inbox"></i>
                <h3>No Records Found</h3>
                <p>No attendance records have been saved yet. Start scanning QR codes to populate this view.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function refreshData() {
            // Reload the page to get fresh data
            window.location.reload();
        }
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
        
        // Add visual indicator for auto-refresh
        let refreshCounter = 30;
        function updateRefreshCounter() {
            refreshCounter--;
            if (refreshCounter <= 0) {
                refreshCounter = 30;
            }
            
            // Update page title to show countdown
            document.title = `Attendance Records (${refreshCounter}s) - Excel View`;
        }
        
        setInterval(updateRefreshCounter, 1000);

        function exportToCSV() {
            const table = document.getElementById('attendanceTable');
            const rows = table.querySelectorAll('tr');
            const csvContent = [];
            
            // Process each row
            rows.forEach(row => {
                const cols = row.querySelectorAll('th, td');
                const rowData = [];
                
                cols.forEach(col => {
                    let cellText = col.textContent.trim();
                    // Clean up the text and remove extra whitespace
                    cellText = cellText.replace(/\s+/g, ' ');
                    // Escape quotes and wrap in quotes if contains comma
                    if (cellText.includes(',') || cellText.includes('"')) {
                        cellText = '"' + cellText.replace(/"/g, '""') + '"';
                    }
                    rowData.push(cellText);
                });
                
                csvContent.push(rowData.join(','));
            });
            
            // Create and download CSV
            const csvString = csvContent.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `attendance_excel_view_${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
