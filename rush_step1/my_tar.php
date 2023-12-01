<?php
function my_tar() {
    $arguments = $_SERVER['argv'];
    array_shift($arguments);
    $tarballName = array_shift($arguments);
    $files = $arguments;
    if (!preg_match('/\.tar$/', $tarballName)) {
        $tarballName .= '.tar';
    }
    
    $filesString = implode(" ", $files);
    $command = "tar -czvf $tarballName $filesString";
    
    echo "Creating tarball: $tarballName" . PHP_EOL;
    echo "Files to include: " . implode(", ", $files) . PHP_EOL;
    
    foreach ($files as $file) {
        if (is_dir($file)) {
            if (!is_absolute_path($file)) {
                echo "The path must be absolute: $file" . PHP_EOL;
                exit;
            }
        }
    }
    
    @system($command);
}

function is_absolute_path($path) {
    return $path[0] === '/';
}

my_tar();
