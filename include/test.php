<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require_once ('./config.php');
        require_once ('./DatabaseConnection.php');
        require_once ('./User.php');
        
        
        $user = new User();
        //$user->create_user("admin@me.me", "123", 0);
        //echo $user->isUserRegistered('admin@me');
        echo print_r($user->authenticate_user("admin@me.me", "123"));
        ?>
    </body>
</html>
