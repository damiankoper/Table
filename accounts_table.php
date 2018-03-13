<?php
use \Table\Table as Table;
use Cl_Com\Cl_Com as Cl_Com;

$database = new Database\Database($options);
$database = $database->init()->connect();
$columns = array(
    "accounts.name" => "Imię",
    "accounts.surname" => "Nazwisko",
    "accounts.user_id" => "Id",
    "accounts.phone" => "Telefon",
    "accounts.email" => "Email",
    "accounts.nip" => "NIP",
    "accounts.register_date" => "Dołączył",
    "(accounts.to_pay_direct + accounts.to_pay_indirect)" => "Do wypłaty",
    "accounts.awaits_payment" => "Wyp",
    "accounts.to_pay_ever" => "Suma wypłat",
    "accounts.recom" => "ID polecającego",
    "NULL " => "E/U",
);
$users = $database->query()
    ->from("accounts", $columns)
    ->order("accounts.register_date", "DESC");
$title = "Użytkownicy";

$table = new Table(array(
    "_title" => $title,
    "_query" => $users,
    "_table" => "accounts",
    "_columns" => $columns,
    "_database" => &$database,
    "_buttonsTemplate" => "accountsButton.html.tegs"
));
$table->setAttributes(array(
    "LP" => "width40 textright noexpand sort num edit",
    "Id" => "hidden id width80",
    "Imię" => "editable",
    "Nazwisko" => "editable",
    "Telefon" => "phone ellipsis editable",
    "Email" => "email editable",
    "Dołączył" => "date",
    "NIP" => "editable noexpand",
    "Do wypłaty" => "cash noexpand width100 sort textright",
    "Suma wypłat" => "cash noexpand width100 sort textright",
    "Wyp" => "bool textright width40 noexpand",
    "E/U" => "settings noexpand width40",
));
$table->setRowColorRules(array(
    "Wyp",
    array(
        1 => "pkf",
        "_default" => "default-color"
    )
));
