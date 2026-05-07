<?php

mysqli_report(MYSQLI_REPORT_OFF);

if (!function_exists('event_identifier')) {
	function event_identifier($name) {
		return '`' . str_replace('`', '``', $name) . '`';
	}
}

if (!function_exists('event_connect')) {
	function event_connect($host, $user, $pass, $dbName, $port) {
		try {
			return @new mysqli($host, $user, $pass, $dbName, $port);
		} catch (mysqli_sql_exception $e) {
			return null;
		}
	}
}

if (!function_exists('event_connect_server')) {
	function event_connect_server($host, $user, $pass, $port) {
		return event_connect($host, $user, $pass, '', $port);
	}
}

if (!function_exists('event_ensure_database')) {
	function event_ensure_database($host, $user, $pass, $dbName, $port) {
		$server = event_connect_server($host, $user, $pass, $port);
		if(!$server) {
			return false;
		}
		$databaseName = event_identifier($dbName);
		$server->query("CREATE DATABASE IF NOT EXISTS {$databaseName} CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
		$server->close();
		return true;
	}
}

if (!function_exists('event_table_exists')) {
	function event_table_exists($conn, $tableName) {
		$tableName = $conn->real_escape_string($tableName);
		$result = $conn->query("SHOW TABLES LIKE '{$tableName}'");
		return $result && $result->num_rows > 0;
	}
}

if (!function_exists('event_column_exists')) {
	function event_column_exists($conn, $tableName, $columnName) {
		$tableName = $conn->real_escape_string($tableName);
		$columnName = $conn->real_escape_string($columnName);
		$result = $conn->query("SHOW COLUMNS FROM {$tableName} LIKE '{$columnName}'");
		return $result && $result->num_rows > 0;
	}
}

if (!function_exists('event_index_exists')) {
	function event_index_exists($conn, $tableName, $indexName) {
		$tableName = $conn->real_escape_string($tableName);
		$indexName = $conn->real_escape_string($indexName);
		$result = $conn->query("SHOW INDEX FROM {$tableName} WHERE Key_name = '{$indexName}'");
		return $result && $result->num_rows > 0;
	}
}

if (!function_exists('event_bootstrap_schema')) {
	function event_bootstrap_schema($conn) {
		$conn->query("CREATE TABLE IF NOT EXISTS `audience` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` text NOT NULL,
		`contact` varchar(50) NOT NULL,
		`email` varchar(100) NOT NULL,
		`address` text NOT NULL,
		`event_id` int(30) NOT NULL,
		`payment_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0= pending, 1 =Paid',
		`attendance_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1= present',
		`status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = for verification,  1 = confirmed,2= declined',
		`date_created` datetime NOT NULL DEFAULT current_timestamp(),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

	$conn->query("CREATE TABLE IF NOT EXISTS `events` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`venue_id` int(30) NOT NULL,
		`event` text NOT NULL,
		`category` varchar(100) NOT NULL DEFAULT 'General',
		`description` text NOT NULL,
		`schedule` datetime NOT NULL,
		`type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Public, 2-Private',
		`audience_capacity` int(30) NOT NULL,
		`payment_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Free,payable',
		`amount` double NOT NULL DEFAULT 0,
		`banner` text NOT NULL,
		`date_created` datetime NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

	$conn->query("CREATE TABLE IF NOT EXISTS `system_settings` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` text NOT NULL,
		`email` varchar(200) NOT NULL,
		`contact` varchar(20) NOT NULL,
		`cover_image` varchar(255) NOT NULL,
		`about_content` text NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

	$conn->query("CREATE TABLE IF NOT EXISTS `users` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` text NOT NULL,
		`username` varchar(200) NOT NULL,
		`password` text NOT NULL,
		`type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=Admin,2=Staff',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

	$conn->query("CREATE TABLE IF NOT EXISTS `venue` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`venue` text NOT NULL,
		`address` text NOT NULL,
		`description` text NOT NULL,
		`rate` float NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

	$conn->query("CREATE TABLE IF NOT EXISTS `venue_booking` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`name` text NOT NULL,
		`address` text NOT NULL,
		`email` varchar(100) NOT NULL,
		`contact` varchar(100) NOT NULL,
		`venue_id` int(30) NOT NULL,
		`duration` varchar(100) NOT NULL,
		`datetime` datetime NOT NULL,
		`status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0-for verification,1=confirmed,2=canceled',
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

	if(!event_column_exists($conn, 'events', 'category')) {
		$conn->query("ALTER TABLE `events` ADD `category` varchar(100) NOT NULL DEFAULT 'General' AFTER `event`");
	}

		if(!event_index_exists($conn, 'audience', 'uniq_audience_event_email')) {
			$conn->query("ALTER TABLE `audience` ADD UNIQUE KEY `uniq_audience_event_email` (`event_id`, `email`)");
		}
	}
}

if (!function_exists('event_bootstrap_seed_data')) {
	function event_bootstrap_seed_data($conn) {
		$conn->query("INSERT IGNORE INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_image`, `about_content`) VALUES
	(1, 'Event Management System', 'eventmatic@gmail.com', '0752356989', 'cover_image.jpg', 'Welcome to the Event Management System.')");

	$conn->query("INSERT IGNORE INTO `users` (`id`, `name`, `username`, `password`, `type`) VALUES
	(1, 'Administrator', 'admin@event.com', '" . md5('admin123') . "', 1)");

	$conn->query("INSERT IGNORE INTO `venue` (`id`, `venue`, `address`, `description`, `rate`) VALUES
	(1, 'Kampala National Theatre', 'Kampala, Uganda', 'A historic venue for cultural performances and events.', 50000),
	(2, 'Makerere University Main Hall', 'Makerere University, Kampala', 'A spacious hall suitable for conferences and seminars.', 30000),
	(3, 'Gulu Sports Stadium', 'Gulu, Uganda', 'A large outdoor stadium ideal for sports events and concerts.', 80000)");

		$conn->query("INSERT IGNORE INTO `events` (`id`, `venue_id`, `event`, `category`, `description`, `schedule`, `type`, `audience_capacity`, `payment_type`, `amount`, `banner`, `date_created`) VALUES
	(1, 1, 'Cultural Day Celebration', 'General', 'A day to celebrate the rich cultural heritage of Uganda through music, dance, and traditional attire.', '2026-06-15 10:00:00', 1, 500, 1, 0, 'admin/assets/uploads/cultural_day.jpg', '2023-04-01 12:00:00'),
	(2, 2, 'Annual Career Fair', 'General', 'An opportunity for students to meet potential employers and explore career opportunities.', '2026-08-20 09:00:00', 1, 1000, 1, 0, 'uploads/career_fair.jpg', '2023-05-10 14:30:00'),
	(3, 3, 'Science and Innovation Symposium', 'General', 'A platform for students and researchers to showcase innovative projects in science and technology.', '2026-09-10 13:00:00', 2, 300, 2, 5000, 'uploads/science_symposium.jpg', '2023-06-05 08:45:00'),
	(4, 1, 'Annual Music Festival', 'General', 'A celebration of music with performances from local and international artists.', '2026-11-05 18:00:00', 1, 800, 2, 10000, 'uploads/music_festival.jpg', '2023-07-20 11:15:00')");
	}
}

$localDbConfig = [];
$localConfigPath = __DIR__ . '/db_config.php';
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
		$conn = event_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
		if ($conn && !$conn->connect_error) {
			break;
		}
		$lastError = $conn ? $conn->connect_error : 'Connection failed';
		if (stripos($lastError, 'unknown database') !== false) {
			if (event_ensure_database($dbHost, $dbUser, $dbPass, $dbName, $dbPort)) {
				$conn = event_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
				if ($conn && !$conn->connect_error) {
					$lastError = '';
					break;
				}
				$lastError = $conn ? $conn->connect_error : 'Connection failed after database creation';
			}
		}
		if (
			stripos($lastError, 'access denied') !== false ||
			stripos($lastError, 'unknown database') !== false
		) {
			break;
		}
	} catch (mysqli_sql_exception $e) {
		$lastError = $e->getMessage();
		if (stripos($lastError, 'unknown database') !== false) {
			if (event_ensure_database($dbHost, $dbUser, $dbPass, $dbName, $dbPort)) {
				$conn = event_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
				if ($conn && !$conn->connect_error) {
					$lastError = '';
					break;
				}
				$lastError = $conn ? $conn->connect_error : 'Connection failed after database creation';
			}
		}
		if (
			stripos($lastError, 'access denied') !== false ||
			stripos($lastError, 'unknown database') !== false
		) {
			break;
		}
	}
}

if (!$conn || $conn->connect_error) {
	die('Could not connect to mysql: ' . $lastError);
}

event_bootstrap_schema($conn);
event_bootstrap_seed_data($conn);
