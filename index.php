<?php
require_once('Conn.php');
require_once('PdfSettings.php');

$action = $_POST["action"];
header('Content-Type: application/json');
$pdfSettings = new PdfSettings();

if ($action == "saveProcessRequest") {
    $settings =  $pdfSettings->ReadSettings();
    $settings->StartProcessRequestDate = date("Y-m-d H:i:s");
    $pdfSettings->ReadSettings($settings);
    echo json_encode($settings);
    die();
} else if ($action == "GetBatchDetail") {
    $settings =  $pdfSettings->ReadSettings();
    echo json_encode($settings);
    die();
}
echo "{'Error':'Invalid Action'}";
