<?php
use \Table\Table as Table;

$database = new Database\Database($options);
$database = $database->init()->connect();

 $dates = $database->query()
            ->from("sm_signups_dates", array("date"))
            ->order("sm_signups_dates.date", "ASC")->all();
        $filter_dates = [];
        foreach ($dates as $date) {
            $filter_dates[$date["date"]] = $date["date"];
        }
 $title = "Zapisy SM";

$columns = array(
    "sm_signups.fullname" => "Imię i nazwisko",
    "sm_signups.signup_id" => "Id",
    "sm_signups.email" => "Email",
    "sm_signups.phone" => "Telefon",
    "sm_signups.comp_name" => "Nazwa firmy",
    "sm_signups.branch" => "Branża",
    "sm_signups.recom_txt" => "Polecany od",
    "sm_signups.date" => "Termin",
    "sm_signups.signup_date" => "Data zapisu",
    "NULL " => "E/U",
);
$users = $database->query()
    ->from("sm_signups", $columns)
    ->order("sm_signups.signup_date", "DESC");
$table = new Table(array(
    "_title" => $title,
    "_query" => $users,
    "_table" => "sm_signups",
    "_columns" => $columns,
    "_database" => &$database,
    "_buttonsTemplate" => "",
    "_print"=>true
));
$table->setAttributes(array(
    "Id" => "hidden id width80",
    "Imię i nazwisko" => "editable",
    "Telefon" => "phone ellipsis editable",
    "Email" => "email editable",
    "Nazwa firmy"=>"editable",
    "Branża"=>"editable",
    "Polecany od"=>"editable",
    "Termin" => "date",
    "Data zapisu" => "date",
    "E/U" => "settings noexpand width40",
));
$table->setFilters(array(
    array(
        "col" => "sm_signups.date",
        "name" => "Termin",
        "values" => $filter_dates,
    )
));