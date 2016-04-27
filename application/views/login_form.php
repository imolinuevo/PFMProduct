<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>My Form</title>
    </head>
    <body>
        <center>
            <?php echo validation_errors(); ?>
            <?php echo form_open('main/login'); ?>
            <h1>Username</h1>
            <input type="text" name="username" value="" />
            <h1>Password</h1>
            <input type="password" name="password" value="" />
            <input type="submit" value="Submit" />
            <?php echo form_close(); ?>
        </center>
    </body>
</html>