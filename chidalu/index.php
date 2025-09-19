<?php
// index.php - main entry point

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request (CORS)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
	http_response_code(200);
	exit();
}

// Load Composer autoload if available
$vendorAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
	require_once $vendorAutoload;
}

$method = $_SERVER["REQUEST_METHOD"];
$uri = $_SERVER["REQUEST_URI"] ?? '/';

// Route map: path => route file
$routeMap = [
	'auth' => __DIR__ . '/routes/api.php',
	'creatives' => __DIR__ . '/routes/creatives_api.php',
	'jobs' => __DIR__ . '/routes/jobs_api.php',
	'messages' => __DIR__ . '/routes/messages_api.php',
	'job-applications' => __DIR__ . '/routes/job_applications_api.php',
	'google-login' => __DIR__ . '/routes/google-login.php',
	'google-callback' => __DIR__ . '/routes/google-callback.php',
];

// Basic router
handleRequest($uri, $method, $routeMap);
exit;

function handleRequest($url, $method, $routeMap)
{
	$url = parse_url($url, PHP_URL_PATH);
	$url = trim($url, '/');
	$parts = $url === '' ? [] : explode('/', $url);

	// Require 'index.php' in the URL and get the segment after it
	$pathSegment = null;
	$indexPhpPos = array_search('index.php', $parts, true);
	if ($indexPhpPos !== false) {
		if (isset($parts[$indexPhpPos + 1])) {
			$pathSegment = $parts[$indexPhpPos + 1];
		}
	} else {
		// No index.php in URL, return clear error
		echo json_encode([
			"statuscode" => -1,
			"status" => "Route must include index.php",
			"hint" => "/chidalu/index.php/{path}?endpoint=..."
		]);
		return;
	}

	if ($pathSegment !== null && isset($routeMap[$pathSegment])) {
		$fullpath = $routeMap[$pathSegment];
		if (is_file($fullpath)) {
			// Included scripts are responsible for sending the response
			require $fullpath;
			return;
		}
	}

	// Default error if no valid route matched
	echo json_encode([
		"statuscode" => -1,
		"status" => "Invalid path after index.php",
		"path" => $pathSegment,
	]);
}


