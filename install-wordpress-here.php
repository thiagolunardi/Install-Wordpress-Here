<?php
set_time_limit ( 60 );
$latest = "https://wordpress.org/latest.tar.gz";

# Downloading
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $latest);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec ($ch);
curl_close ($ch);

$file = fopen("latest.tar.gz", "w+");
fputs($file, $data);
fclose($file);

# Extract to here
if (file_exists ( 'latest.tar' ))
    unlink ( 'latest.tar' );
$p = new PharData('latest.tar.gz');
$p->decompress();

$phar = new PharData('latest.tar');
$phar->extractTo('./', null, true);

# Delete temp files
unset($phar);
Phar::unlinkArchive('latest.tar');
unset($p);
Phar::unlinkArchive('latest.tar.gz');

$items = scandir ( "wordpress" );
foreach ( $items as $item ) 
    if ($item != "." && $item != "..") 
        rename ("./wordpress/". $item, "./" . $item );
delTree("./wordpress");

# Remove this file
unlink('install-wordpress-here.php');

# Installing
header('Location: http:/index.php', true, 303);
die();

function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file)
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    return rmdir($dir); 
}
?>