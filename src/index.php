<?php
// Do not execute from cli
if (php_sapi_name() == "cli") {
    exit("Do NOT execute from CLI!");
}

// Return response with JSON format
header('Content-Type: application/json; charset=UTF-8');

// Check Values
if(isset($_GET["ip"]) && filter_var($_GET["ip"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $ip = $_GET["ip"];
} elseif (isset($_SERVER["REMOTE_ADDR"]) && filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $ip = $_SERVER["REMOTE_ADDR"];
} else {
    echo json_encode(
        array("status" => "error", "msg" => "[invalid] Only check ipv4 addresses."),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    ) . "\n";
    exit;
}

// Load Config Values
require_once('config.php');

// Connect to Database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    exit;
}

$stmt = $mysqli->prepare("
    SELECT network,country,region,region_name FROM icloud_private_relay_ip 
    WHERE start_ip <= INET6_ATON(?) and end_ip >= INET6_ATON(?)
");
$stmt->bind_param('ss', $ip, $ip);
$stmt->execute();
$stmt->bind_result($network,$country,$region,$region_name);
$stmt->fetch();

$arr["status"] = is_null($network) ? "unmatched" : "matched";;

if ($arr["status"] == "matched") {
    $arr["ip"] = $ip;
    $arr["network"] = $network ?? '';
    $arr["country"] = $country ?? '';
    $arr["region"] = $region ?? '';
    $arr["regionName"] = $region_name ?? '';
} else {
    $arr["msg"] = 'Only check ipv4 addresses.';
}

// Output result
echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

?>