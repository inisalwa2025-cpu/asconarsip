<?php
$dbHost = getenv('DB_HOST') ?: 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com';
$dbPort = (int) (getenv('DB_PORT') ?: 4000);
$dbUser = getenv('DB_USER') ?: '4Xf17Qz4MD3Lkf1.root';
$dbPassword = getenv('DB_PASSWORD') ?: 'N56jqc4yMz69wiSH';
$dbName = getenv('DB_NAME') ?: 'fortune500';
$dbSslCa = getenv('DB_SSL_CA') ?: (__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'isrgrootx1.pem');
