<?php

$sourceDir = '/home/capm1443/public_html/storage/app/public/';
$targetDir = '/home/capm1443/public_html/public/storage/';
$logFile = '/home/capm1443/public_html/cron_log.txt';

if (!is_dir($sourceDir)) {
    file_put_contents($logFile, "Direktori sumber tidak ditemukan: $sourceDir\n", FILE_APPEND);
    exit;
}

function copyFilesRecursively($sourceDir, $targetDir, &$fileCopied) {
    $files = scandir($sourceDir);

    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $file;
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($sourcePath)) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0777, true);
                }
                copyFilesRecursively($sourcePath, $targetPath, $fileCopied);
            } else {
                if (!file_exists($targetPath)) {
                    if (copy($sourcePath, $targetPath)) {
                        $fileCopied = true;
                    }
                }
            }
        }
    }
}

$fileCopied = false;

copyFilesRecursively($sourceDir, $targetDir, $fileCopied);

if ($fileCopied) {
    $currentTime = date('Y-m-d H:i:s');
    file_put_contents($logFile, "Perubahan terdeteksi dan file baru disalin pada $currentTime\n", FILE_APPEND);
}

?>
