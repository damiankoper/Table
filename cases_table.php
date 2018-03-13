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
$columns = array(
                "cases.nth"=>"LP",
                "cases.case_id"=>"Id",
                "concat(contacts.name,' ',contacts.surname)"=>"Klient",
                "contacts.phone"=>"Telefon",
                "CASE cases.status
                    WHEN 0 THEN 'W TRAKCIE'
                    WHEN 1 THEN 'ZAKOŃCZONY'
                    WHEN -1 THEN 'ODRZUCONY' 
                END" =>"Status",
                "cases.section"=>"Dział",
                "cases.description"=>"Cel",
                "cases.seconded"=>"Oddelegowane do",
                "cases.checklist"=>"CL",
                " cases.checklist_AW "=>"CL_AW",
                "cases.comments"=>"Dzisiejszy com",
                "NULL "=>"E/U",
                "NULL  "=>"Info"
                );
$cases = $database->query()
            ->from("cases", $columns)
            ->join("contacts", "contacts.contact_id = cases.contact_id", array(), "LEFT")
            ->order("cases.nth", "DESC");
$title = "Sprawy";
$table = new Table(array("_title"=>$title,"_query"=>$cases,"_table"=>"cases","_columns"=>$columns,"_database"=>&$database,"_buttonsTemplate"=>"buttonsStatus.html.tegs"));
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
        "col"=>"cases.status",
        "name"=>"Status",
        "values"=>array(
            0=>"W TRAKCIE",
            1=>"ZAKOŃCZONY",
            -1=>"ODRZUCONY"
            )
    ),
    "Oddelegowane do"=>"cases.seconded",
    "Dział"=>"cases.section"
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
$table->setAddForm("admin_templates/dodaj_sprawe.html.tegs", $scope);

function insert($post, $table, $database)
{
    $response = new \stdClass();
    $response->type="success";
    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'))) {
        unset($post["action"]);
        $id = $database->query()->from("cases")->genId(8, "case_id");
           
        try {
            switch($post["contact_if"]){
                case "new":
                    $contact = $database->query()->from("contacts")->genId(8, "contact_id");
                    $database->query()->from("contacts")
                       ->save(array(
                           "contact_id"=>"$contact",
                           "name"=>$post["contacts_name"],
                           "surname"=>$post["contacts_surname"],
                           "email"=>$post["contacts_email"],
                           "phone"=>$post["contacts_phone"],
                           "pesel"=>$post["contacts_pesel"],
                           "cases"=>$post["contacts_cases"],
                       ));
                break;
                case "old":
                    $contact = $post["cases_contact_id"];
                break;
                case "none":
                    $contact = "";
                break;
            }
            $post["cases_seconded"] = (is_null($post["cases_seconded"]))?"":$post["cases_seconded"];
            $post["cases_section"] = (is_null($post["cases_section"]))?"":$post["cases_section"];
            $row = $database->query()
            ->from("cases")
            ->save(array(
                "case_id"=>"$id",
                "contact_id"=>"$contact",
                "description"=>$post["cases_description"],
                "seconded"=>$post["cases_seconded"],
                "section"=>$post["cases_section"],
            ));
        } catch (\Exception $e) {
            $response->type="error";
        }
    }

    return json_encode($response);
}
