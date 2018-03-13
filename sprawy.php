
<?php
require_once "Table_autoloader.php";

include "cases_table.php";

//ajax wywołuje tylko siebie i w zależności od parametru jest refresh, update, insert, save
//zabezpieczenie przed SQL injection
//POST dla bezpieczeńśtwa operacji na bazie danych
//GET dla danych filtrowania i kolumn
//AA NIE WIEM JUŻ SAM
if (!isset($_POST["action"])) {
    $_POST["action"] = "";
}
switch ($_POST["action"]) {
    case "refresh" :
        echo $table->render_table();
        break;
    case "remove" :
        echo $table->remove_row($_POST["id"],"case_id");
        break;
    case "edit" :
        echo $table->edit_row($_POST["id"], $_POST["data"],"case_id");
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
            "cases",
            $_POST["target"],
            $_POST["id"],
            $dataArray,
            "case_id"
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
            "cases",
            $_POST["id"],
            $dataArray,
            "case_id"
        );
        break;
    default :
        include "login_control.php";
        break;
}
