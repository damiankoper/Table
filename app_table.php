<?php
use \Table\Table as Table;
use Cl_Com\Cl_Com as Cl_Com;
use Cl_App\Cl_App as Cl_App;
use KIK\KIK as KIK;

$database = new Database\Database($options);
$database = $database->init()->connect();

function colorStatus($text)
{
    if ($text === "W TRAKCIE") {
        return "<span style='color:Orange;'>{$text}</span>";
    }
    if ($text === "ODRZUCONY") {
        return "<span style='color:red;'>{$text}</span>";
    }
    if ($text === "ZAKOŃCZONY") {
        return "<span style='color:green;'>{$text}</span>";
    }
}
function lastCl($text)
{
    return Cl_Com::lastCl($text);
}
function lastCom($text)
{
    return Cl_Com::lastCom($text);
}
function lastNotDone($text)
{
    return Cl_App::lastNotDone($text);
}
function getFullName($text)
{
    $kik = new KIK(array("_rawData" => $text));
    return $kik->getFullName();
}
$columns = array(
    "application.content" => "Imię i nazwisko z KIK",
    "CASE application.status
                    WHEN 0 THEN 'W TRAKCIE'
                    WHEN 1 THEN 'ZAKOŃCZONY'
                    WHEN -1 THEN 'ODRZUCONY'
                END" => "Status",
    "application.app_id" => "Id",
    "application.user_id" => "Id użytkownika",
    "concat(accounts.name,' ',accounts.surname)" => "Dodający",
    "application.connected_with" => "Dod kred",
    "application.checklist" => "CL",
    "application.comments" => "Dzisiejszy com",
    "application.progress" => "Ostatni postęp",
    "application.paid" => "Zap",
    "application.amount" => "Kwota",
    "application.date" => "Data dodania",
    "NULL " => "E/U",
    "NULL  " => "Info"
);
$app = $database->query()
    ->from("application", $columns)
    ->join("accounts", "accounts.user_id = application.user_id", array(), "LEFT")
    ->order("application.date", "DESC");
$title = "Wnioski";
$table = new Table(array(
    "_title" => $title, 
    "_query" => $app,
    "_table" => "application", 
    "_columns" => $columns, 
    "_database" => &$database,
    "_buttonsTemplate"=>"buttonsStatus.html.tegs"
    ));
$table->setAttributes(array(
    "Id" => "hidden id width80",
    "Status" => "width80 sort hidden",
    "E/U" => "settings noexpand width40",
    "Info" => "slidable noexpand width40",
    "Zap" => "bool textright width40",
    "Kwota" => "cash noexpand width100 sort textright",
    "Id użytkownika" => "hidden",
    "Dodający" => "hidden",
    "Dod kred" => "hidden width80",
    "Data dodania" => "hidden date",
));
$table->setFunctions(array(
    "Status" => "colorStatus",
    "CL" => "lastCl",
    "CL_AW" => "lastCl",
    "Dzisiejszy com" => "lastCom",
    "Ostatni postęp" => "lastNotDone",
    "Imię i nazwisko z KIK" => "getFullName"
));
$table->setRowColorRules(array(
    "Dział",
    array(
        "PKFO" => "pkfo",
        "PKF" => "pkf",
        "NR" => "nr",
        "SM" => "sm",
        "_default" => "default-color"
    )
));

$connectedWithList = $database->query()
    ->from("application", array(
    "application.app_id",
    "application.content"
))->order("date", "DESC")->all();

$cList = [];
foreach ($connectedWithList as $value) {
    $kik_cList = new KIK(array(
        "_rawData" => $value["content"]
    ));
    $info = array();
    $info["id"] = $value["id"];
    unset($value["id"]);
    $info["text"] = $kik_cList->getFullName();
    $cList[] = $info;
}


$accounts = $database->query()
    ->from("accounts", array("user_id" => "id", "name", "surname"))
    ->order("surname", "DESC")
    ->all();
$accounts_info = [];
foreach ($accounts as $value) {
    $info = array();
    $info["id"] = $value["id"];
    unset($value["id"]);
    $info["text"] = implode(" ", $value);
    $accounts_info[] = $info;
}


$table->setSlidable(function ($row, $row_info) use ($database, $cList, $accounts_info) {
    $CL = new Cl_Com(array(
        "_cl" => $row["CL"],
        "_com" => $row["Dzisiejszy com"]
    ));
    $CL_APP = new Cl_App(array(
        "_clData" => json_decode($row["Ostatni postęp"])
    ));

    $connectedWith = $database->query()
        ->from("application", array(
        "application.app_id",
        "application.content"
    ))
        ->where("id = ?", $row["Dod kred"])->all();
    $kik = new KIK(array(
        "_rawData" => $row["Imię i nazwisko z KIK"],
        "_connectedWith" => $connectedWith,
        "_date" => $row["Data dodania"],
        "_userID" => $row["Id użytkownika"],
        "_userName" => $row["Dodający"],
        "_accounts" => $accounts_info,
        "_cList" => $cList,
        "_zap"=>$row["Zap"],
    ));
    return "<div style='display:flex'>" . $kik->renderMainInfo() . "</div>
            <div style='display:flex'>" . $CL->renderCL() . $CL->renderCom() . "</div>
            <div style='display:flex'>" . $kik->renderKIK() . "</div>
            <div style='display:flex'>" . $CL_APP->renderCL() . "</div>";
});
$table->setFilters(array(
    array(
        "col" => "application.status",
        "name" => "Status",
        "values" => array(
            0 => "W TRAKCIE",
            1 => "ZAKOŃCZONY",
            -1 => "ODRZUCONY"
        )
    ),
));




//$scope = array("contacts" => $contact_info);
//$table->setAddForm("admin_templates/dodaj_polecanego.html.tegs", $scope);

