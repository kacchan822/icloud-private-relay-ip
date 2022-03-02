<?php
// Only execute CLI
if (php_sapi_name() != 'cli') {
    exit;
}

// Load Config Values
require_once('config.php');
date_default_timezone_set('Asia/Tokyo');

// Connect to Database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    exit;
}

// GET iCloud Private Reray Egress IP addresses
$csv_data = new NoRewindIterator(new SplFileObject(ICLOUD_PRIVATE_RELAY_IP_LIST_CSV));
$csv_data->setFlags(SplFileObject::READ_CSV);

// Update Database 
$mysqli->begin_transaction();
echo "[" . date("Y/m/d H:i:s") . "] Starting Update Database.\n";
try {
    $mysqli->query("RENAME TABLE icloud_private_relay_ip TO icloud_private_relay_ip_old");
    $mysqli->query("CREATE TABLE icloud_private_relay_ip LIKE icloud_private_relay_ip_old");

    $stmt = $mysqli->prepare("
        INSERT INTO icloud_private_relay_ip(network, start_ip, end_ip, country, region, region_name) 
            VALUES(?, INET6_ATON(?), INET6_ATON(?), ?, ? ,?)
    ");
    foreach($csv_data as $line) {
        list($start_ip, $cidr) = explode("/", $line[0]);
        // only ipv4 address
        // TODO: ipv6
        if (!filter_var($start_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            continue;
        }
        $data = array(
            $line[0],  // network
            $start_ip,  // start_ip
            long2ip(ip2long($start_ip) + (2 ** (32 - $cidr)) - 1),  // end_ip
            $line[1],  // country
            $line[2],  // region
            $line[3],  // region_name
        );
        $stmt->bind_param('ssssss', ...$data);
        $stmt->execute();
    }
    $mysqli->query("DROP TABLE icloud_private_relay_ip_old");
    $mysqli->commit();
    echo "[" . date("Y/m/d H:i:s") . "] Finished Update Database.\n";
} catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    echo "[" . date("Y/m/d H:i:s") . "] FAILD Update Database.\n";
}
?>