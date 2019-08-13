<?php
$zip = new ZipArchive;
if ($zip->open('file.zip') === TRUE) {
    $zip->extractTo('file/');
    $zip->close();
    echo 'ok';
} else {
    echo 'failed';
}
?>
