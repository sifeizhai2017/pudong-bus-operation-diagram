<?php
$zip = new ZipArchive;
if ($zip->open('file.zip') === TRUE) {
    $zip->extractTo('/my/destination/dir/');
    $zip->close();
    echo 'ok';
} else {
    echo 'failed';
}
?>
