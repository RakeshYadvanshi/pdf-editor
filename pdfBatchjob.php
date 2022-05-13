<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once('vendor/setasign/fpdf/fpdf.php');
require_once('vendor/autoload.php');
require_once('Conn.php');
require_once('PdfSettings.php');
require_once('Phplogger.php');

use setasign\Fpdi\Fpdi;

$settings = new PdfSettings();
$logger = new Phplogger();
$pdfSettings = $settings->ReadSettings();
if ($pdfSettings->Completed ==  true) {
    $pdfSettings->LastJobRunDate =  date("Y-m-d H:i:s");
    $settings->WriteSettings($pdfSettings);
    $logger->WriteLog("Request completed \r\n");;
    die();
}

$db = new DbInterface();
$sql = "SELECT *, DATE_FORMAT(post_date, '%d-%m-%Y') post_date_formatted FROM wp_posts WHERE guid like '%.pdf%'";
if ($pdfSettings->LastDocumentProcessDate != "") {
    //$sql = $sql . " and post_modified >='" . $pdfSettings->LastDocumentProcessDate . "'";
}
//$sql = $sql . " limit 100";

$result = $db->getTable($sql);
$logger->WriteLog($sql);

$files = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $b = new stdClass();
        $b->webUrl = str_replace("http://www.grv.org.au/", "", $row["guid"]);
        $b->webUrl = str_replace("https://www.grv.org.au/", "", $b->webUrl);
        $b->webUrl = str_replace("http://localhost/grv/wp-content", "", $b->webUrl);

        // $b->webUrl = str_replace(WP_SITEURL, "", $row["guid"]);
        $b->publishDate =  $row["post_date"];
        $b->publishDateFormatted =  $row["post_date_formatted"];
        $b->modifiedDate =  $row["post_modified"];
        array_push($files, $b);
    }
} else {
    $pdfSettings->LastJobRunDate =  date("Y-m-d H:i:s");
    $pdfSettings->Completed =  true;
    $settings->WriteSettings($pdfSettings);
    $logger->WriteLog("no pending records");
    die();
}
$logger->WriteLog(json_encode($files));

foreach ($files as $file) {
    $filePath = __DIR__ . "/" . $file->webUrl;

    if (file_exists($filePath) != 1) {
        $logger->WriteLog("File does not exists: " . $filePath);
        continue;
    }


    try {
        $pdf = new Fpdi();
        $logger->WriteLog("processing:" . $filePath);
        // get the page count
        $pageCount = $pdf->setSourceFile($filePath);
        // iterate through all pages
        $logger->WriteLog("looping through page :" . $filePath);
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // import a page
            $logger->WriteLog("importing page :" . $filePath);
            $templateId = $pdf->importPage($pageNo);

            $logger->WriteLog("adding page :" . $filePath);
            $pdf->AddPage();
            // use the imported page and adjust the page size
            $logger->WriteLog("use template  :" . $filePath);
            $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

            if ($pageNo == 1) {
                $logger->WriteLog("set font  :" . $filePath);
                $pdf->SetFontSize(8);
                $pdf->SetFont('Helvetica');
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetXY(2, 2);
                $logger->WriteLog("setting date  :" . $filePath);
                $pdf->Write(5, "<a>The content on this page was published on " . $file->publishDateFormatted . ". If you notice any inaccuracies, please report it here    </a>", "https://www.grv.org.au/report-outdated-information/?durl=".urlencode("http://www.grv.org.au/". $file->webUrl), true);
                $logger->WriteLog("date set  :" . $filePath);
            }
        }

        $logger->WriteLog("writing file:" . $filePath);
        // Output the new PDF
        $pdf->Output("F", $filePath);
    } catch (\Exception  $ex) {
        $logger->WriteLog(json_encode($ex));
        $logger->WriteLog("<br/><br/>" . 'Using Shell<br/>');
        $logger->WriteLog("ghostscript -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . "\"" . str_replace(".pdf", "popopopopopopop.pdf", $filePath)  . "\" " . "\"" . $filePath . "\"");

        shell_exec("ghostscript -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . "\"" . str_replace(".pdf", "popopopopopopop.pdf", $filePath) . "\" " . "\"" . $filePath . "\"");


        $logger->WriteLog("deleting " . $filePath);
        unlink($filePath);
        $logger->WriteLog("renaming from  " . str_replace(".pdf", "popopopopopopop.pdf", $filePath) . "  to   " . $filePath);
        rename(str_replace(".pdf", "popopopopopopop.pdf", $filePath), $filePath);

        $pdf = new Fpdi();
        $logger->WriteLog("processing:" . $filePath);
        // get the page count
        $pageCount = $pdf->setSourceFile($filePath);
        $logger->WriteLog("looping through page :" . $filePath);
        // iterate through all pages
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // import a page
            $logger->WriteLog("importing page :" . $filePath);
            $templateId = $pdf->importPage($pageNo);

            $logger->WriteLog("adding page :" . $filePath);
            $pdf->AddPage();
            // use the imported page and adjust the page size
            $logger->WriteLog("use template  :" . $filePath);
            $pdf->useTemplate($templateId, ['adjustPageSize' => true]);

            if ($pageNo == 1) {
                $logger->WriteLog("set font  :" . $filePath);
                $pdf->SetFontSize(8);
                $pdf->SetFont('Helvetica');
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetXY(2, 2);
                $logger->WriteLog("setting date  :" . $filePath);
                $pdf->Write(5, "The content on this page was published on " . $file->publishDateFormatted . ". If you notice any inaccuracies, please report it here    ", "https://www.grv.org.au/report-outdated-information/", true);
                $logger->WriteLog("date set  :" . $filePath);
            }
        }
        $logger->WriteLog("writing file:" . $filePath);
        // Output the new PDF
        $pdf->Output("F", $filePath);
    }

    $pdfSettings->LastDocumentProcessDate =  $file->modifiedDate;
    $pdfSettings->LastJobRunDate =  date("Y-m-d H:i:s");
    $settings->WriteSettings($pdfSettings);
}
$pdfSettings->LastJobRunDate =  date("Y-m-d H:i:s");
$settings->WriteSettings($pdfSettings);
$logger->WriteLog("processed");
