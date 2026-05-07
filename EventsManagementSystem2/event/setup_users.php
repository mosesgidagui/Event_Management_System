<?php
/**
 * Setup Script - Create Admin and Student Accounts
 * This script updates the database with new user accounts
 */

// Database connection
$localDbConfig = [];
$localConfigPath = __DIR__ . '/admin/db_config.php';
if (file_exists($localConfigPath)) {
    $loadedConfig = include $localConfigPath;
    if (is_array($loadedConfig)) {
        $localDbConfig = $loadedConfig;
    }
}

$dbHost = getenv('EVENT_DB_HOST') ?: ($localDbConfig['host'] ?? '127.0.0.1');
$dbUser = getenv('EVENT_DB_USER') ?: ($localDbConfig['user'] ?? 'root');
$dbPass = getenv('EVENT_DB_PASS');
if ($dbPass === false) {
    $dbPass = $localDbConfig['pass'] ?? '';
}
$dbName = getenv('EVENT_DB_NAME') ?: ($localDbConfig['name'] ?? 'event_db');
$dbPortEnv = getenv('EVENT_DB_PORT');
if ($dbPortEnv === false || $dbPortEnv === '') {
    $dbPortEnv = isset($localDbConfig['port']) ? (string)$localDbConfig['port'] : false;
}
$portsToTry = [];
if ($dbPortEnv !== false && $dbPortEnv !== '') {
    $portsToTry[] = (int)$dbPortEnv;
}
$portsToTry[] = 3306;
$portsToTry[] = 3307;
$portsToTry = array_values(array_unique($portsToTry));

$conn = null;
$lastError = '';
foreach ($portsToTry as $dbPort) {
    try {
        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        if (!$conn->connect_error) {
            break;
        }
        $lastError = $conn->connect_error;
        if (
            stripos($lastError, 'access denied') !== false ||
            stripos($lastError, 'unknown database') !== false
        ) {
            break;
        }
    } catch (mysqli_sql_exception $e) {
        $lastError = $e->getMessage();
        if (
            stripos($lastError, 'access denied') !== false ||
            stripos($lastError, 'unknown database') !== false
        ) {
            break;
        }
    }
}

if (!$conn || $conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $lastError]));
}

try {
    // Delete existing accounts
    $conn->query("DELETE FROM users WHERE id IN (1, 2, 3)");
    
    // Create new admin account
    $admin_email = 'admin@event.com';
    $admin_password = md5('admin 123');
    $admin_insert = "INSERT INTO users (id, name, username, password, type) VALUES (1, 'Administrator', '$admin_email', '$admin_password', 1)";
    
    if (!$conn->query($admin_insert)) {
        throw new Exception("Failed to create admin account: " . $conn->error);
    }
    
    // Create first student account
    $student1_name = 'Winnie Student';
    $student1_email = 'winnie@stud.event.com';
    $student1_password = md5('student123');
    $student1_insert = "INSERT INTO users (id, name, username, password, type) VALUES (2, '$student1_name', '$student1_email', '$student1_password', 3)";
    
    if (!$conn->query($student1_insert)) {
        throw new Exception("Failed to create first student account: " . $conn->error);
    }
    
    // Create second student account
    $student2_name = 'Pamela Student';
    $student2_email = 'pamela@stud.event.com';
    $student2_password = md5('student123');
    $student2_insert = "INSERT INTO users (id, name, username, password, type) VALUES (3, '$student2_name', '$student2_email', '$student2_password', 3)";
    
    if (!$conn->query($student2_insert)) {
        throw new Exception("Failed to create second student account: " . $conn->error);
    }
    
    $conn->close();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Users created successfully',
        'accounts' => [
            'admin' => [
                'email' => 'admin@event.com',
                'password' => 'admin 123',
                'role' => 'Administrator'
            ],
            'student1' => [
                'email' => 'winnie@stud.event.com',
                'password' => 'student123',
                'role' => 'Student'
            ],
            'student2' => [
                'email' => 'pamela@stud.event.com',
                'password' => 'student123',
                'role' => 'Student'
            ]
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
