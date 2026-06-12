<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ROSSUM ATTENDANCE</title>
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
      min-height: 100vh;
      color: #333;
    }

    .container {
      display: flex;
      min-height: 100vh;
      margin: 0 auto;
      background: white;
      box-shadow: 0 0 30px rgba(0,0,0,0.1);
    }

    .sidebar {
      width: 320px;
      background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
      color: white;
      padding: 2rem;
      position: relative;
      overflow-y: auto;
    }

    .sidebar h1 {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 2rem;
      text-align: center;
      color: #ecf0f1;
    }

    .sidebar h1 i {
      margin-right: 0.5rem;
      color: #3498db;
    }

    .control-group {
      margin-bottom: 1.5rem;
      background: rgba(255,255,255,0.1);
      padding: 1rem;
      border-radius: 10px;
      backdrop-filter: blur(10px);
    }

    .control-group h3 {
      font-size: 1rem;
      margin-bottom: 0.5rem;
      color: #ecf0f1;
      display: flex;
      align-items: center;
    }

    .control-group h3 i {
      margin-right: 0.5rem;
      color: #3498db;
    }

    .control-group input {
      width: 100%;
      padding: 0.75rem;
      border: none;
      border-radius: 8px;
      background: rgba(255,255,255,0.9);
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .control-group input:focus {
      outline: none;
      background: white;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3);
    }

    .btn {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
    }

    .btn i {
      margin-right: 0.5rem;
    }

    .btn-primary {
      background: linear-gradient(45deg, #3498db, #2980b9);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }

    .btn-danger {
      background: linear-gradient(45deg, #e74c3c, #c0392b);
      color: white;
    }

    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }

    .btn-success {
      background: linear-gradient(45deg, #27ae60, #229954);
      color: white;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
    }

    .btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none !important;
    }

    .main-content {
      flex: 1;
      padding: 2rem;
      overflow-y: auto;
      background: #f8f9fa;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      background: white;
      padding: 1.5rem;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .header h2 {
      font-size: 2rem;
      color: #2c3e50;
      display: flex;
      align-items: center;
    }

    .header h2 i {
      margin-right: 0.5rem;
      color: #3498db;
    }

    .current-time {
      background: linear-gradient(45deg, #3498db, #2980b9);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      font-weight: 600;
      display: flex;
      align-items: center;
      flex-direction: column;
      text-align: center;
    }

    .current-time i {
      margin-right: 0.5rem;
      color: #3498db;
    }

    .scanner-section {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      margin-bottom: 2rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }

    .scanner-section.hidden {
      display: none;
    }

    #scanner-container {
      width: 100%;
      max-width: 500px;
      height: 350px;
      margin: 1rem auto;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
      border: 3px solid #3498db;
    }

    .scanner-info {
      color: #7f8c8d;
      font-size: 1.1rem;
      margin-top: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .scanner-info i {
      margin-right: 0.5rem;
      color: #3498db;
    }

    .attendance-table {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
    }

    .table-header {
      background: linear-gradient(45deg, #34495e, #2c3e50);
      color: white;
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .table-header h3 {
      font-size: 1.3rem;
      display: flex;
      align-items: center;
    }

    .table-header h3 i {
      margin-right: 0.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #ecf0f1;
    }

    th {
      background: #f8f9fa;
      font-weight: 600;
      color: #2c3e50;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tr:hover {
      background: #f8f9fa;
    }

    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
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
      background: #eaecee;
      color: #7f8c8d;
    }

    .summary-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .summary-card {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .summary-card:hover {
      transform: translateY(-5px);
    }

    .summary-card.present {
      border-left: 5px solid #27ae60;
    }

    .summary-card.late {
      border-left: 5px solid #f39c12;
    }

    .summary-card.absent {
      border-left: 5px solid #e74c3c;
    }

    .summary-number {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .summary-number.present {
      color: #27ae60;
    }

    .summary-number.late {
      color: #f39c12;
    }

    .summary-number.absent {
      color: #e74c3c;
    }

    .summary-label {
      font-size: 1.1rem;
      color: #7f8c8d;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .summary-label i {
      margin-right: 0.5rem;
    }

    .qr-section {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .qr-section.hidden {
      display: none;
    }

    .qr-section h3 {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
      color: #2c3e50;
      display: flex;
      align-items: center;
    }

    .qr-section h3 i {
      margin-right: 0.5rem;
      color: #3498db;
    }

    .qr-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
    }

    .qr-card {
      background: #f8f9fa;
      padding: 1.5rem;
      border-radius: 10px;
      text-align: center;
      border: 2px solid #ecf0f1;
      transition: all 0.3s ease;
    }

    .qr-card:hover {
      border-color: #3498db;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .qr-card h4 {
      margin-top: 1rem;
      font-size: 0.9rem;
      color: #2c3e50;
      font-weight: 600;
    }

    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 1.5rem;
      border-radius: 10px;
      color: white;
      font-weight: 600;
      z-index: 1000;
      transform: translateX(400px);
      transition: transform 0.3s ease;
    }

    .notification.show {
      transform: translateX(0);
    }

    .notification.success {
      background: linear-gradient(45deg, #27ae60, #229954);
    }

    .notification.error {
      background: linear-gradient(45deg, #e74c3c, #c0392b);
    }

    .row-male {
      background-color: rgba(52, 152, 219, 0.1) !important;
    }

    .row-female {
      background-color: rgba(233, 30, 99, 0.1) !important;
    }

    .row-male:hover {
      background-color: rgba(52, 152, 219, 0.2) !important;
    }

    .row-female:hover {
      background-color: rgba(233, 30, 99, 0.2) !important;
    }

    .qr-toggle-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      font-size: 1.5rem;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .qr-toggle-btn:hover {
      background: #2980b9;
      transform: scale(1.1);
    }

    .btn-print {
      background: linear-gradient(45deg, #9b59b6, #8e44ad);
      color: white;
    }

    .btn-print:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(155, 89, 182, 0.4);
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        padding: 1rem;
      }

      .main-content {
        padding: 1rem;
      }

      .header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
      }

      .summary-cards {
        grid-template-columns: 1fr;
      }

      .qr-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      }
    }

    @media print {
      body {
        background: white;
      }

      .container {
        box-shadow: none;
      }

      .sidebar,
      .header,
      .scanner-section,
      .summary-cards,
      .attendance-table,
      .qr-toggle-btn,
      .notification {
        display: none !important;
      }

      .main-content {
        padding: 0;
        background: white;
      }

      .qr-section {
        display: block !important;
      }

      .qr-section h3 {
        text-align: center;
        margin-bottom: 2rem;
        page-break-after: avoid;
      }

      .qr-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        page-break-inside: avoid;
      }

      .qr-card {
        border: 2px solid #2c3e50;
        padding: 1rem;
        page-break-inside: avoid;
        background: white;
        box-shadow: none;
      }

      .qr-card h4 {
        font-size: 0.7rem;
        word-wrap: break-word;
        margin-top: 0.5rem;
        color: #000;
      }

      .qr-card canvas,
      .qr-card img {
        max-width: 100%;
        height: auto;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">

      <div class="control-group">
        <h3><i class="fas fa-clock"></i>Cutoff Time</h3>
        <input type="time" id="cutoff-time" value="12:30" />
      </div>

      <div class="control-group">
        <h3><i class="fas fa-hourglass-half"></i>Late Threshold (min)</h3>
        <input type="number" id="late-threshold" value="5" min="1" max="60" />
      </div>

      <button id="scan-btn" class="btn btn-primary">
        <i class="fas fa-qrcode"></i>Start Scanner
      </button>

      <button id="stop-scan-btn" class="btn btn-danger" disabled>
        <i class="fas fa-stop"></i>Stop Scanner
      </button>

      <button id="export-btn" class="btn btn-success">
        <i class="fas fa-download"></i>Export Data
      </button>

      <button id="reset-btn" class="btn" style="background: linear-gradient(45deg, #95a5a6, #7f8c8d); color: white;">
        <i class="fas fa-refresh"></i>Reset All
      </button>

      <button id="print-qr-btn" class="btn btn-print">
        <i class="fas fa-print"></i>Print QR Codes
      </button>

      <!-- Added link to view attendance records -->
      <a href="view_attendance.php" class="btn" style="background: linear-gradient(45deg, #8e44ad, #9b59b6); color: white;">
        <i class="fas fa-table"></i>View Records
      </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="header">
        <h2><i class="fas fa-users"></i>Rossum Student Attendance</h2>
        <div class="current-time">
          <i class="fas fa-clock"></i>
          <span id="current-time"></span>
          <br>
          <i class="fas fa-calendar"></i>
          <span id="current-date"></span>
        </div>
      </div>

      <!-- Scanner Section -->
      <div id="scanner-section" class="scanner-section hidden">
        <h3 style="margin-bottom: 1rem; color: #2c3e50;"><i class="fas fa-camera"></i> QR Code Scanner</h3>
        <div id="scanner-container"></div>
        <p class="scanner-info">
          <i class="fas fa-info-circle"></i>
          Point the camera at a student's QR code to record attendance
        </p>
      </div>

      <!-- Summary Cards -->
      <div class="summary-cards">
        <div class="summary-card present">
          <div class="summary-number present" id="summary-present">0</div>
          <div class="summary-label">
            <i class="fas fa-check-circle"></i>Present
          </div>
        </div>
        <div class="summary-card late">
          <div class="summary-number late" id="summary-late">0</div>
          <div class="summary-label">
            <i class="fas fa-clock"></i>Late
          </div>
        </div>
        <div class="summary-card absent">
          <div class="summary-number absent" id="summary-absent">0</div>
          <div class="summary-label">
            <i class="fas fa-times-circle"></i>Absent
          </div>
        </div>
        <div class="summary-card present" style="border-left: 5px solid #3498db;">
          <div class="summary-number present" id="boys-present" style="color: #3498db;">0</div>
          <div class="summary-label">
            <i class="fas fa-male"></i>Boys Present
          </div>
        </div>
        <div class="summary-card present" style="border-left: 5px solid #e91e63;">
          <div class="summary-number present" id="girls-present" style="color: #e91e63;">0</div>
          <div class="summary-label">
            <i class="fas fa-female"></i>Girls Present
          </div>
        </div>
      </div>

      <!-- Attendance Table -->
      <div class="attendance-table">
        <div class="table-header">
          <h3><i class="fas fa-table"></i>Attendance Records</h3>
        </div>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th><i class="fas fa-id-card"></i> Student ID</th>
                <th><i class="fas fa-user"></i> Name</th>
                <th><i class="fas fa-info-circle"></i> Status</th>
                <th><i class="fas fa-clock"></i> Time</th>
              </tr>
            </thead>
            <tbody id="attendance-body">
              <!-- Attendance data will be populated here -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- QR Codes Section -->
      <div class="qr-section hidden" id="qr-section">
        <h3><i class="fas fa-qrcode"></i>Student QR Codes</h3>
        <div id="qr-codes-container" class="qr-grid"></div>
      </div>
    </div>
  </div>

  <!-- Notification -->
  <div id="notification" class="notification"></div>

  <!-- QR Toggle Button -->
  <button id="qr-toggle-btn" class="qr-toggle-btn" title="Toggle QR Codes">
    <i class="fas fa-qrcode"></i>
  </button>

  <script>
    const students = [
      { id: "S001", name: "ALBANIA, JUSTINE JOHN L.", lrn: "117908140064", gender: "male" },
      { id: "S002", name: "ALEGRE, SANDRO C.", lrn: "222501120148", gender: "male" },
      { id: "S003", name: "ARGUILLES, CYRUZ RENZ E.", lrn: "136534120689", gender: "male" },
      { id: "S004", name: "BALUYA, DANNE EARLL GABBRIEL A.", lrn: "136534140459", gender: "male" },
      { id: "S005", name: "BERMUDEZ, DEZCARTES REY F.", lrn: "136536130601", gender: "male" },
      { id: "S006", name: "CABUSAO, RAFAEL S.", lrn: "136648130778", gender: "male" },
      { id: "S007", name: "CAPILI, ALLEN GABRIEL L.", lrn: "406492150684", gender: "male" },
      { id: "S008", name: "DACARA, PRINCE GHAVRIEL P.", lrn: "136546131324", gender: "male" },
      { id: "S009", name: "ELCANO, KELVIN T.", lrn: "136535130620", gender: "male" },
      { id: "S010", name: "GAMBOT, JOEFFER REX S.", lrn: "136534140465", gender: "male" },
      { id: "S011", name: "GARCIA, JOHN LEMAR C.", lrn: "136543130250", gender: "male" },
      { id: "S012", name: "HIPOL, JOHAN ALEXIS D.", lrn: "136534120672", gender: "male" },
      { id: "S013", name: "JUNSAY, KIERAN SHIN N.", lrn: "136534140634", gender: "male" },
      { id: "S014", name: "LAZO, JANNIEL GILL O.", lrn: "136534140708", gender: "male" },
      { id: "S015", name: "MANGENTE, ELIJAH GWAIN C.", lrn: "136532140109", gender: "male" },
      { id: "S016", name: "MARQUEZ, KARL S.", lrn: "108805110063", gender: "male" },
      { id: "S017", name: "MONTENEGRO, JOSHUIA V.", lrn: "136534140300", gender: "male" },
      { id: "S018", name: "PEREZ, JOHN CHRISTIAN B.", lrn: "136546110272", gender: "male" },
      { id: "S019", name: "PILI, REXEL LUTHER T.", lrn: "482830150084", gender: "male" },
      { id: "S020", name: "SANTOS, ANDREI B.", lrn: "136529140994", gender: "male" },
      { id: "S021", name: "SUGUITAN, JOHN MICHAEL S.", lrn: "136543140010", gender: "male" },
      { id: "S022", name: "TECSON, JOSE MARI C.", lrn: "136534140310", gender: "male" },
      { id: "S023", name: "TORRES, ETHAN BEIGE D.", lrn: "", gender: "male" },
      { id: "S024", name: "TUASON, ADRIAN KELLY T.", lrn: "136532130264", gender: "male" },
      { id: "S025", name: "VILLAMOR, EARL STEPHEN P.", lrn: "482814150145", gender: "male" },
      { id: "S026", name: "ABALOYAN, MARIA ZHULLIANNE M.", lrn: "406481150107", gender: "female" },
      { id: "S027", name: "BARBADILLO, ANN KIRSTINE A.", lrn: "136532130269", gender: "female" },
      { id: "S028", name: "BOSH, FRANZENEL G.", lrn: "136534140615", gender: "female" },
      { id: "S029", name: "DUMO, WELLA NICOLE SAY D.", lrn: "136537130593", gender: "female" },
      { id: "S030", name: "FACUNLA, ARIANNEY MAE F.", lrn: "136548130746", gender: "female" },
      { id: "S031", name: "JACOB, JASMIN S.", lrn: "136648140820", gender: "female" },
      { id: "S032", name: "MENDOZA, ASHLY P.", lrn: "482790150162", gender: "female" },
      { id: "S033", name: "PALAMING, MYLES M.", lrn: "406485150296", gender: "female" },
      { id: "S034", name: "SALAZAR, ELIJAHGEN B.", lrn: "136534140219", gender: "female" },
      { id: "S035", name: "SAN JUAN, DOMINIQUE KYLA YHANELLE A.", lrn: "136647130699", gender: "female" }
    ];

    let attendanceRecords = {};
    let dailyScannedStudents = new Set();
    let html5QrcodeScanner;
    let scannerActive = false;
    let lastScanTime = 0;
    const SCAN_COOLDOWN = 2000; // 2 seconds cooldown between scans

    // DOM Elements
    const cutoffTimeInput = document.getElementById('cutoff-time');
    const lateThresholdInput = document.getElementById('late-threshold');
    const scanBtn = document.getElementById('scan-btn');
    const stopScanBtn = document.getElementById('stop-scan-btn');
    const exportBtn = document.getElementById('export-btn');
    const resetBtn = document.getElementById('reset-btn');
    const scannerSection = document.getElementById('scanner-section');
    const qrCodesContainer = document.getElementById('qr-codes-container');
    const notification = document.getElementById('notification');
    const boysPresentEl = document.getElementById('boys-present');
    const girlsPresentEl = document.getElementById('girls-present');

    function checkAndResetAtMidnight() {
      const lastDate = localStorage.getItem('lastAttendanceDate');
      const currentDate = getCurrentDateString();
      
      if (lastDate && lastDate !== currentDate) {
        console.log("[v0] New day detected, auto-resetting attendance...");
        // Clear old data
        clearOldAttendanceData();
        // Reset current data
        students.forEach(student => {
          attendanceRecords[student.id] = null;
        });
        dailyScannedStudents.clear();
        saveAttendanceData();
        saveDailyScannedStudents();
        showNotification('Attendance automatically reset for new day', 'success');
      }
      
      // Update last date
      localStorage.setItem('lastAttendanceDate', currentDate);
    }

    // Initialize
    function initializeApp() {
      console.log("[v0] Initializing app...");
      
      // Wait for DOM to be fully loaded
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          setTimeout(startApp, 100); // Small delay to ensure all elements are rendered
        });
      } else {
        setTimeout(startApp, 100);
      }
    }
    
    function startApp() {
      console.log("[v0] Starting app...");
      
      // Get DOM elements with error checking
      const attendanceBody = document.getElementById('attendance-body');
      const currentTimeEl = document.getElementById('current-time');
      const currentDateEl = document.getElementById('current-date');
      
      console.log("[v0] DOM elements found:", {
        attendanceBody: !!attendanceBody,
        currentTimeEl: !!currentTimeEl,
        currentDateEl: !!currentDateEl
      });
      
      if (!currentTimeEl || !currentDateEl) {
        console.error("[v0] Required DOM elements not found!");
        return;
      }
      
      checkAndResetAtMidnight();
      
      loadDailyScannedStudents();
      
      // Initialize functions
      loadAttendanceData();
      renderAttendanceTable();
      updateCurrentTime();
      updateSummary();
      updateGenderSummary();
      
      // Set up intervals
      setInterval(updateCurrentTime, 1000);
      setInterval(function() {
        loadAttendanceData();
        renderAttendanceTable();
        updateSummary();
        updateGenderSummary();
      }, 5000);
      
      setInterval(checkAndResetAtMidnight, 60000);
      
      console.log("[v0] App initialized successfully");
    }

    function getCurrentDateString() {
      const today = new Date();
      return today.toISOString().split('T')[0];
    }

    function loadDailyScannedStudents() {
      const currentDate = getCurrentDateString();
      const savedData = localStorage.getItem(`dailyScanned_${currentDate}`);
      
      if (savedData) {
        dailyScannedStudents = new Set(JSON.parse(savedData));
        console.log("[v0] Loaded scanned students:", dailyScannedStudents.size);
      } else {
        dailyScannedStudents = new Set();
        saveDailyScannedStudents();
      }
    }

    function saveDailyScannedStudents() {
      const currentDate = getCurrentDateString();
      localStorage.setItem(`dailyScanned_${currentDate}`, JSON.stringify([...dailyScannedStudents]));
    }

    function isStudentAlreadyScannedToday(studentId) {
      return dailyScannedStudents.has(studentId);
    }

    // Load attendance data from localStorage
    function loadAttendanceData() {
      const currentDate = getCurrentDateString();
      const savedData = localStorage.getItem(`attendanceRecords_${currentDate}`);
      
      if (savedData) {
        attendanceRecords = JSON.parse(savedData);
      } else {
        // Initialize empty records for all students
        students.forEach(student => {
          attendanceRecords[student.id] = null;
        });
        saveAttendanceData();
      }
    }

    function saveAttendanceData() {
      const currentDate = getCurrentDateString();
      localStorage.setItem(`attendanceRecords_${currentDate}`, JSON.stringify(attendanceRecords));
    }

    // Show notification
    function showNotification(message, type = 'success') {
      notification.textContent = message;
      notification.className = `notification ${type}`;
      notification.classList.add('show');
      
      // Clear previous timeout if exists
      if (window.notificationTimeout) {
        clearTimeout(window.notificationTimeout);
      }
      
      // Auto-hide notification after 3 seconds
      window.notificationTimeout = setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
          notification.textContent = '';
        }, 300);
      }, 3000);
    }

    // Update current time and date
    function updateCurrentTime() {
      const now = new Date();
      const currentTimeEl = document.getElementById('current-time');
      const currentDateEl = document.getElementById('current-date');
      
      if (currentTimeEl) {
        currentTimeEl.textContent = now.toLocaleTimeString('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        });
      } else {
        console.log("[v0] Warning: currentTimeEl not found");
      }
      
      if (currentDateEl) {
        currentDateEl.textContent = now.toLocaleDateString('en-US', {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      } else {
        console.log("[v0] Warning: currentDateEl not found");
      }
    }

    // Determine attendance status based on scan time
    function determineStatus(scanTime) {
      const [cutoffHours, cutoffMinutes] = cutoffTimeInput.value.split(':').map(Number);
      const cutoffTime = new Date();
      cutoffTime.setHours(cutoffHours, cutoffMinutes, 0, 0);

      const lateThreshold = parseInt(lateThresholdInput.value);
      const lateTime = new Date(cutoffTime.getTime() + lateThreshold * 60000);

      if (scanTime <= cutoffTime) {
        return 'PRESENT';
      } else if (scanTime <= lateTime) {
        return 'LATE';
      } else {
        return 'ABSENT';
      }
    }

    // Format time as 12-hour format with AM/PM
    function formatTime(date) {
      return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
      });
    }

    // Render attendance table
    function renderAttendanceTable() {
      const attendanceBody = document.getElementById('attendance-body');
      
      attendanceBody.innerHTML = '';
      
      students.forEach(student => {
        const record = attendanceRecords[student.id];
        const row = document.createElement('tr');
        
        if (student.gender === 'male') {
          row.classList.add('row-male');
        } else if (student.gender === 'female') {
          row.classList.add('row-female');
        }

        // Student ID
        const idCell = document.createElement('td');
        idCell.textContent = student.id;
        idCell.style.fontWeight = '600';
        row.appendChild(idCell);

        // Name
        const nameCell = document.createElement('td');
        nameCell.textContent = student.name;
        row.appendChild(nameCell);

        // Status
        const statusCell = document.createElement('td');
        if (record) {
          const statusClass = `status-${record.status.toLowerCase()}`;
          statusCell.innerHTML = `<span class="status-badge ${statusClass}">${record.status}</span>`;
        } else {
          statusCell.innerHTML = '<span class="status-badge status-not-scanned">Not Scanned</span>';
        }
        row.appendChild(statusCell);

        // Time
        const timeCell = document.createElement('td');
        timeCell.textContent = record ? record.time : '-';
        timeCell.style.fontFamily = 'monospace';
        row.appendChild(timeCell);

        attendanceBody.appendChild(row);
      });
      
      updateAttendanceSummary();
    }

    // Update attendance summary
    function updateAttendanceSummary() {
      let presentCount = 0;
      let lateCount = 0;
      let absentCount = 0;

      students.forEach(student => {
        const record = attendanceRecords[student.id];
        if (record) {
          if (record.status === 'PRESENT') presentCount++;
          else if (record.status === 'LATE') lateCount++;
          else if (record.status === 'ABSENT') absentCount++;
        } else {
          absentCount++;
        }
      });

      document.getElementById('summary-present').textContent = presentCount;
      document.getElementById('summary-late').textContent = lateCount;
      document.getElementById('summary-absent').textContent = absentCount;
    }

    // Update gender summary
    function updateGenderSummary() {
      let boysPresentCount = 0;
      let girlsPresentCount = 0;

      students.forEach(student => {
        const record = attendanceRecords[student.id];
        if (record && (record.status === 'PRESENT' || record.status === 'LATE')) {
          if (student.gender === 'male') boysPresentCount++;
          else if (student.gender === 'female') girlsPresentCount++;
        }
      });

      if (boysPresentEl) boysPresentEl.textContent = boysPresentCount;
      if (girlsPresentEl) girlsPresentEl.textContent = girlsPresentCount;
    }

    // Generate QR codes for all students
    function generateQRCodes() {
      const qrCodesContainer = document.getElementById('qr-codes-container');
      
      qrCodesContainer.innerHTML = '';

      students.forEach(student => {
        const card = document.createElement('div');
        card.className = 'qr-card';

        const qrDiv = document.createElement('div');
        qrDiv.id = `qr-${student.id}`;
        card.appendChild(qrDiv);

        const label = document.createElement('h4');
        label.textContent = `${student.name} (${student.id})`;
        card.appendChild(label);

        qrCodesContainer.appendChild(card);

        // Generate QR code
        new QRCode(qrDiv, {
          text: student.id,
          width: 120,
          height: 120,
          colorDark: "#2c3e50",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
      });
    }

    // Send attendance to server
    async function sendAttendanceToServer(studentName, status, time, studentId) {
      try {
        const response = await fetch('save_attendance.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ studentName, status, time, studentId })
        });

        const data = await response.json();
        
        if (data.success) {
          console.log('Attendance saved to server');
          return true;
        } else {
          console.error('Server error:', data.error);
          if (data.error.includes('already been recorded')) {
            showNotification(`Student has already been scanned today!`, 'error');
          } else {
            showNotification(data.error || 'Failed to save to server', 'error');
          }
          return false;
        }
      } catch (error) {
        console.error('Network error:', error);
        showNotification('Network error occurred', 'error');
        return false;
      }
    }

    // Export attendance data
    function exportAttendanceData() {
      const csvContent = "data:text/csv;charset=utf-8,";
      const headers = ["Student ID", "Name", "Status", "Time", "Date"];
      const rows = [headers];

      students.forEach(student => {
        const record = attendanceRecords[student.id];
        const row = [
          student.id,
          student.name,
          record ? record.status : 'ABSENT',
          record ? record.time : '-',
          new Date().toLocaleDateString()
        ];
        rows.push(row);
      });

      const csvString = rows.map(row => row.join(",")).join("\n");
      const encodedUri = encodeURI(csvContent + csvString);
      
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", `attendance_${new Date().toISOString().split('T')[0]}.csv`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      showNotification('Attendance data exported successfully!');
    }

    // Update summary
    function updateSummary() {
      updateAttendanceSummary();
      updateGenderSummary();
    }

    // Clear old attendance data from localStorage
    function clearOldAttendanceData() {
      const currentDate = getCurrentDateString();
      console.log("[v0] Current date:", currentDate);
      
      // Get all localStorage keys
      const keys = Object.keys(localStorage);
      
      // Remove old attendance and scanned data that doesn't match today's date
      keys.forEach(key => {
        if ((key.startsWith('attendanceRecords_') || key.startsWith('dailyScanned_')) && 
            !key.includes(currentDate)) {
          console.log("[v0] Removing old data:", key);
          localStorage.removeItem(key);
        }
      });
    }

    // Event Listeners
    scanBtn.addEventListener('click', async () => {
      if (scannerActive) return;

      try {
        scannerSection.classList.remove('hidden');
        scanBtn.innerHTML = '<div class="loading"></div>Starting...';
        scanBtn.disabled = true;

        html5QrcodeScanner = new Html5Qrcode("scanner-container");

        const config = { fps: 10, qrbox: 250 };

        await html5QrcodeScanner.start(
          { facingMode: "environment" },
          config,
          async qrCodeMessage => {
            const currentTime = Date.now();
            if (currentTime - lastScanTime < SCAN_COOLDOWN) {
              return; // Ignore scan if within cooldown period
            }
            lastScanTime = currentTime;

            const student = students.find(s => s.id === qrCodeMessage);
            if (student) {
              if (isStudentAlreadyScannedToday(student.id)) {
                showNotification(`${student.name} has already been scanned today!`, 'error');
                return;
              }

              const scanTime = new Date();
              const status = determineStatus(scanTime);
              const timeString = formatTime(scanTime);

              // Try to save to server first
              const serverSaved = await sendAttendanceToServer(student.name, status, timeString, student.id);
              
              if (serverSaved) {
                // Only update local data if server save was successful
                attendanceRecords[student.id] = {
                  status: status,
                  time: timeString
                };

                dailyScannedStudents.add(student.id);
                saveDailyScannedStudents();
                saveAttendanceData();
                renderAttendanceTable();
                updateSummary();

                showNotification(`${student.name}: ${status}`, 'success');
              } else {
                // If server save failed, don't update local data
                showNotification(`Failed to record attendance for ${student.name}`, 'error');
              }
            } else {
              showNotification(`Invalid student ID: ${qrCodeMessage}`, 'error');
            }
          },
          errorMessage => {
            // Silent error handling for continuous scanning
          }
        );

        scannerActive = true;
        scanBtn.innerHTML = '<i class="fas fa-qrcode"></i>Scanner Active';
        stopScanBtn.disabled = false;
        
      } catch (error) {
        showNotification('Unable to start camera', 'error');
        scanBtn.innerHTML = '<i class="fas fa-qrcode"></i>Start Scanner';
        scanBtn.disabled = false;
        scannerSection.classList.add('hidden');
      }
    });

    stopScanBtn.addEventListener('click', async () => {
      if (scannerActive && html5QrcodeScanner) {
        try {
          await html5QrcodeScanner.stop();
          scannerActive = false;
          scannerSection.classList.add('hidden');
          scanBtn.innerHTML = '<i class="fas fa-qrcode"></i>Start Scanner';
          scanBtn.disabled = false;
          stopScanBtn.disabled = true;
          showNotification('Scanner stopped');
        } catch (error) {
          showNotification('Error stopping scanner', 'error');
        }
      }
    });

    exportBtn.addEventListener('click', exportAttendanceData);

    resetBtn.addEventListener('click', async () => {
      if (confirm('Are you sure you want to reset all attendance records for today? This action cannot be undone.')) {
        try {
          const response = await fetch('reset_attendance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
          });
          
          const data = await response.json();
          
          if (data.success) {
            // Clear daily attendance records
            const currentDate = getCurrentDateString();
            localStorage.removeItem(`attendanceRecords_${currentDate}`);
            localStorage.removeItem(`dailyScanned_${currentDate}`);
            
            // Reset in-memory data
            students.forEach(student => {
              attendanceRecords[student.id] = null;
            });
            dailyScannedStudents.clear();
            
            // Save empty data
            saveAttendanceData();
            saveDailyScannedStudents();
            
            renderAttendanceTable();
            updateSummary();
            showNotification('All attendance records have been reset for today', 'success');
          } else {
            showNotification(data.message || 'Failed to reset server records', 'error');
          }
        } catch (error) {
          console.error('[v0] Reset error:', error);
          showNotification('Error resetting records: ' + error.message, 'error');
        }
      }
    });

    // QR toggle functionality
    function toggleQRSection() {
      const qrSection = document.getElementById('qr-section');
      const toggleBtn = document.getElementById('qr-toggle-btn');
      
      if (qrSection.classList.contains('hidden')) {
        qrSection.classList.remove('hidden');
        generateQRCodes(); // Generate QR codes when showing
        toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
        toggleBtn.title = 'Hide QR Codes';
      } else {
        qrSection.classList.add('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-qrcode"></i>';
        toggleBtn.title = 'Show QR Codes';
      }
    }

    function printQRCodes() {
      const qrSection = document.getElementById('qr-section');
      
      // Show QR section if hidden
      if (qrSection.classList.contains('hidden')) {
        qrSection.classList.remove('hidden');
        generateQRCodes();
      }
      
      // Wait for QR codes to render, then print
      setTimeout(() => {
        window.print();
      }, 500);
    }

    // QR toggle button event listener
    document.getElementById('qr-toggle-btn').addEventListener('click', toggleQRSection);

    document.getElementById('qr-toggle-btn').addEventListener('dblclick', printQRCodes);

    document.getElementById('print-qr-btn').addEventListener('click', printQRCodes);

    // Initialize the application
    initializeApp();
  </script>
</body>
</html>
