<?php
use \Table\Table as Table;

$database = new Database\Database($options);
$database = $database->init()->connect();

$columns = array(
    "contacts.name" => "Imię",
    "contacts.surname" => "Nazwisko",
    "contacts.pesel" => "Pesel",
    "contacts.contact_id" => "Id",
    "contacts.email" => "Email",
    "contacts.phone" => "Telefon",
    "contacts.recom" => "Polecany od",
    "contacts.branch" => "Branża",
    "contacts.info" => "Info",
    "NULL " => "E/U",
);
$title = "Kontakty";

$users = $database->query()
    ->from("contacts", $columns)
    ->order("contacts.contact_date", "DESC");
$table = new Table(array(
    "_title" => $title,
    "_query" => $users,
    "_table" => "contacts",
    "_columns" => $columns,
    "_database" => &$database,
    "_buttonsTemplate" => "",
    "_print" => false
));
$table->setAttributes(array(
    "Id" => "hidden id width80",
    "Imię" => "editable",
    "Nazwisko"=>"editable",
    "Info"=>"editable",
    "Telefon" => "phone ellipsis editable",
    "Email" => "email editable",
    "Pesel" => "hidden",
    "Branża" => "editable",
    "Polecany od" => "editable",
    "Termin" => "date",
    "Data zapisu" => "date",
    "E/U" => "settings noexpand width40",
));

$table->setAddForm("admin_templates/dodaj_kontakt.html.tegs", $scope);

function insert($post, $table, $database)
{
    $response = new \stdClass();
    $response->type = "success";
    if ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'))) {
        unset($post["action"]);
        try {
            $contact = $database->query()->from("contacts")->genId(8, "contact_id");
            $database->query()->from("contacts")
                ->save(array(
                "contact_id" => "$contact",
                "name" => $post["contacts_name"],
                "surname" => $post["contacts_surname"],
                "email" => $post["contacts_email"],
                "phone" => $post["contacts_phone"],
                "pesel" => $post["contacts_pesel"],
                "recom" => $post["contacts_recom"],
            ));
        } catch (\Exception $e) {
            $response->type = "error";
        }
    }

    return json_encode($response);
}