<?php
namespace Cl_App;

class Cl_AppAjax extends \Table\Base
{

    public static function updateClCom($database, $table, $id, $dataArray, $id_alias)
    {
        $database->query()
            ->from($table)
            ->where("$id_alias = ?", $id)
            ->save($dataArray);
        $response = new \stdClass();
        $response->type="success";
        return \json_encode($response);
    }
}