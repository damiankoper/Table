<?php
require_once "Table_autoloader.php";

include "contacts_table.php";

if (!isset($_POST["action"])) {
    $_POST["action"] = "";
}
switch ($_POST["action"]) {
    case "refresh" :
        echo $table->render_table();
        break;
    case "remove" :
        echo $table->remove_row($_POST["id"],"contact_id");
        break;
    case "edit" :
        echo $table->edit_row($_POST["id"], $_POST["data"], "contact_id");
        break;
    default :
        include "login_control.php";
        break;
}
