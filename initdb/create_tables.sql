BEGIN;
USE icloudprivaterelayip;
DROP TABLE IF EXISTS icloud_private_relay_ip;
CREATE TABLE icloud_private_relay_ip(
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    network VARCHAR(43) NOT NULL,
    start_ip VARBINARY(16) NOT NULL,
    end_ip VARBINARY(16) NOT NULL,
    country VARCHAR(2) NOT NULL,
    region VARCHAR(5),
    region_name VARCHAR(32)
);
CREATE INDEX index_start_ip on icloud_private_relay_ip(start_ip);
CREATE INDEX index_end_ip on icloud_private_relay_ip(end_ip);
CREATE INDEX index_country on icloud_private_relay_ip(country);
COMMIT;