<?php
require_once "Table_autoloader.php";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $database = new Database\Database($options);
    $database = $database->init()->connect();
    $admin = $database->query()
        ->from("admin",array("id", "password"))
        ->where("login=?", filter_input(INPUT_POST, 'login-1'))
        ->all();
    if (crypt(filter_input(INPUT_POST, "password-1"), $admin[0]["password"]) === $admin[0]["password"]) {
        $_SESSION['admin_session'] = $admin[0]['id'];
        $invalid = false;
        if(empty($_GET['u']))$goto = "index.php";
        else $goto = $_GET['u'];
        header("Location: $goto");
    }
    else {
        $invalid = true;
    }
}
else $invalid = false;
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta https-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="admin_css/admin_main.css">
</head>

<body>
<form class="login-form" method="post">
                <p style="font-size:3em; margin-bottom:0;">
                    PKFO
                </p>
                <p style="margin-top:0;">
                    Panel administratora
                </p>
                <fieldset>
                    <?php
                    if ($invalid) {
                        echo "<label>Złe dane logowania.</label>";
                    }
                    ?>
                    <legend>Logowanie</legend>
                    <label>Login:</label>
                    <input name="login-1" type="text" />
                    <label>Hasło:</label>
                    <input name="password-1" type="password" />
                    <input type="submit" class="button-1" value="Zaloguj się" />
                </fieldset>
            </form>
            
</body>

</html>