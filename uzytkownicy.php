<?php
require_once "Table_autoloader.php";

include "accounts_table.php";

if (!isset($_POST["action"])) {
    $_POST["action"] = "";
}
switch ($_POST["action"]) {
    case "refresh" :
        echo $table->render_table();
        break;
    case "remove" :
        echo $table->remove_row($_POST["id"]);
        break;
    case "edit" :
        echo $table->edit_row($_POST["id"], $_POST["data"], "user_id");
        break;
    case "insert" :
        $data = array();
        parse_str($_POST["data"], $data);
        echo insert($data, $table, $database);
        break;
    case "payment" :
        $database->query()
            ->from("accounts")
            ->where("user_id = ?", $_POST["id"])
            ->save(array(
                'to_pay_direct' => 0,
                'to_pay_indirect' => 0,
                'awaits_payment' => 0
            ));
        $response = new \stdClass();
        $response->type = "success";
        echo \json_encode($response);
        break;
    default :
        include "login_control.php";
        break;
}
