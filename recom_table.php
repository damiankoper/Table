<?php
use \Table\Table as Table;
use Cl_Com\Cl_Com as Cl_Com;
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
function lastCl($text){
    return Cl_Com::lastCl($text);
}
function lastCom($text){
    return Cl_Com::lastCom($text);
}
$title = "Polecani Klienci";
$columns = array(
                "recom.nth"=>"LP",
                "recom.recom_id"=>"Id",
                "concat(contacts.name,' ',contacts.surname)"=>"Klient",
                "contacts.phone"=>"Telefon",
                "CASE recom.status
                    WHEN 0 THEN 'W TRAKCIE'
                    WHEN 1 THEN 'ZAKOŃCZONY'
                    WHEN -1 THEN 'ODRZUCONY' 
                END" =>"Status",
                "recom.section"=>"Dział",
                "recom.description"=>"Cel",
                "recom.seconded"=>"Oddelegowane do",
                "recom.checklist"=>"CL",
                " recom.checklist_AW "=>"CL_AW",
                "recom.comments"=>"Dzisiejszy com",
                "NULL "=>"E/U",
                "NULL  "=>"Info"
                );
$recom = $database->query()
            ->from("recom", $columns)
            ->join("contacts", "contacts.contact_id = recom.contact_id", array(), "LEFT")
            ->order("recom.nth", "DESC");
$table = new Table(array("_title"=>$title,"_query"=>$recom,"_table"=>"recom","_columns"=>$columns,"_database"=>&$database,"_buttonsTemplate"=>"buttonsStatus.html.tegs"));
$table->setAttributes(array(
    "LP"=>"width40 textright noexpand sort num",
    "Id"=>"hidden id width80",
    "Telefon"=>"phone ellipsis",
    "Cel"=>"editable sort nl2br",
    "Klient"=>"sort noexpand",
    "Oddelegowane do"=>"hidden",
    "Status"=>"width80 sort hidden",
    "Dział"=>"editable noexpand width40 sort hidden",
    "E/U"=>"settings noexpand width40",
    "Info"=>"slidable noexpand width40",
));
$table->setFunctions(array(
    "Status"=>"colorStatus",
    "CL"=>"lastCl",
    "CL_AW"=>"lastCl",
    "Dzisiejszy com"=>"lastCom"
));
$table->setRowColorRules(array(
    "Dział",
    array("PKFO" => "pkfo",
          "PKF" => "pkf",
          "NR" => "nr",
          "SM" => "sm",
          "_default" => "default-color")
));
$table->setSlidable(function ($row, $row_info) {
    $CL = new Cl_Com(array(
        "_cl"=>$row["CL"],
        "_com"=>$row["Dzisiejszy com"]
    ));
    $CL_AW = new Cl_Com(array(
        "_cl"=>$row["CL_AW"],
        "_AW"=>true
    ));
    return "<div style='display:flex'>".$CL->renderCL().$CL->renderCom()."</div>".$CL_AW->renderCL();
});
$table->setFilters(array(
    array(
        "col"=>"recom.status",
        "name"=>"Status",
        "values"=>array(
            0=>"W TRAKCIE",
            1=>"ZAKOŃCZONY",
            -1=>"ODRZUCONY"
            )
    ),
    "Oddelegowane do"=>"recom.seconded",
    "Dział"=>"recom.section"
    )
);

$contacts = $database->query()
            ->from("contacts", array("contact_id"=>"id","name","surname"))
            ->order("surname", "DESC")
            ->all();
$contact_info = [];
foreach ($contacts as $value) {
    $info = array();
    $info["id"]=$value["id"];
    unset($value["id"]);
    $info["text"]=implode(" | ", $value);
    $contact_info[]=$info;
}
$scope = array("contacts"=>$contact_info);
$table->setAddForm("admin_templates/dodaj_polecanego.html.tegs", $scope);

function insert($post, $table, $database)
{
    $response = new \stdClass();
    $response->type="success";
    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'))) {
        unset($post["action"]);
        $id = $database->query()->from("recom")->genId(8, "recom_id");
           
        try {
            switch($post["contact_if"]){
                case "new":
                    $contact = $database->query()->from("contacts")->genId(8,"contact_id");
                    $database->query()->from("contacts")
                       ->save(array(
                           "contact_id"=>"$contact",
                           "name"=>$post["contacts_name"],
                           "surname"=>$post["contacts_surname"],
                           "email"=>$post["contacts_email"],
                           "phone"=>$post["contacts_phone"],
                           "pesel"=>$post["contacts_pesel"],
                           "recom"=>$post["contacts_recom"],
                       ));
                break;
                case "old":
                    $contact = $post["recom_contact_id"];
                break;
                case "none":
                    $contact = "";
                break;
            }
            $post["recom_seconded"] = (is_null($post["recom_seconded"]))?"":$post["recom_seconded"];
            $post["recom_section"] = (is_null($post["recom_section"]))?"":$post["recom_section"];
            $row = $database->query()
            ->from("recom")
            ->save(array(
                "recom_id"=>"$id",
                "contact_id"=>"$contact",
                "description"=>$post["recom_description"],
                "seconded"=>$post["recom_seconded"],
                "section"=>$post["recom_section"],
            ));
        } catch (\Exception $e) {
            $response->type="error";
        }
    }

    return json_encode($response);
}
