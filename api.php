<?php<?php
// 1. Clear cross-origin constraints so your React app on port 5174 can access this data
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS requests gracefully
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 2. Establish connection to your MySQL Database
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "campusflow_db"; // Matches your phpMyAdmin schema

$conn = new mysqli($servername, $username, $password, $dbname);

// 3. Fallback error validation response if the MySQL engine is offline
if ($conn->connect_error) {
    echo json_encode([
        "error" => true,
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit();
}

// 4. Fetch day transit routing logs from your schedules dataset table
$sql = "SELECT route, time, type, driver FROM schedules ORDER BY id ASC";
$result = $conn->query($sql);

$schedules = array();

if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $schedules[] = array(
                "route"  => $row['route'],
                "time"   => $row['time'],
                "type"   => $row['type'],
                "driver" => $row['driver']
            );
        }
    }
    // Output valid array dataset back to the app stream loop
    echo json_encode($schedules);
} else {
    echo json_encode([
        "error" => true,
        "message" => "Query failure execution error: " . $conn->error
    ]);
}

// Close the active thread link cleanly
$conn->close();
?>