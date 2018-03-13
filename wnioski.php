
<?php
require_once "Table_autoloader.php";

include "app_table.php";

//ajax wywołuje tylko siebie i w zależności od parametru jest refresh, update, insert, save
//zabezpieczenie przed SQL injection
//POST dla bezpieczeńśtwa operacji na bazie danych
//GET dla danych filtrowania i kolumn
//AA NIE WIEM JUŻ SAM
$database->execute("update application set paid = 0");
if (!isset($_POST["action"])) {
    $_POST["action"] = "";
}
switch ($_POST["action"]) {
    case "refresh" :
        echo $table->render_table();
        break;
    case "remove" :
        echo $table->remove_row($_POST["id"], "app_id");
        break;
    case "edit" :
        echo $table->edit_row($_POST["id"], $_POST["data"], "app_id");
        break;
    case "insert" :
        $data = array();
        parse_str($_POST["data"], $data);
        echo insert($data, $table, $database);
        break;
    case "clcom-update" :
        $dataArray = array();
        if (isset($_POST["checklist"])) {
            $dataArray["checklist"] = $_POST["checklist"];
        }
        if (isset($_POST["comments"])) {
            $dataArray["comments"] = $_POST["comments"];
        }
        echo Cl_Com\Cl_ComAjax::updateClCom(
            $database,
            "application",
            $_POST["target"],
            $_POST["id"],
            $dataArray,
            "app_id"
        );
        break;
    case "clapp-update" :
        $dataArray = array();
        if (isset($_POST["progress"])) {
            $dataArray["progress"] = $_POST["progress"];
        }
        echo Cl_App\Cl_AppAjax::updateClCom(
            $database,
            "application",
            $_POST["id"],
            $dataArray,
            "app_id"
        );
        break;
    case "kik-update" :
        $dataArray = array();
        if (isset($_POST["content"]) && isset($_POST["connected_with"]) && isset($_POST["user_id"])) {
            $dataArray = $_POST;
            unset($dataArray["action"]);
        }
        echo KIK\KIKAjax::updateKIK(
            $database,
            "application",
            $_POST["id"],
            $dataArray
        );
        break;
    case "kik-split" :
        $dataArray = array();
        if (isset($_POST["amount"])) {
            $dataArray = $_POST;
            unset($dataArray["action"]);
        }
        echo KIK\KIKAjax::splitCash(
            $database,
            "application",
            $_POST["id"],
            $dataArray
        );
        break;
    case "edit-status" :
        $dataArray = array();
        if (isset($_POST["status"])) {
            $dataArray = $_POST;
            unset($dataArray["action"]);
        }
        echo KIK\KIKAjax::updateKIK(
            $database,
            "application",
            $_POST["id"],
            $dataArray
        );
        break;
    default :
        include "login_control.php";
        break;
}
