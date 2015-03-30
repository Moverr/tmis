<?php
require_once("qrlib.php");
# The text to use in the QR Code. If not value is passed, it uses the current time stamp
$text = !empty($_GET['value'])? $_GET['value']: strtotime('now');

QRcode::png($text,
    $outfile = false,
    $level = QR_ECLEVEL_L,
    $size = 7,
    $margin = 0,
    $saveandprint = false);
?>