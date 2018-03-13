<?php
namespace Cl_Com;

class Cl_ComAjax extends \Table\Base
{

    public static function updateClCom($database, $table, $target, $id, $dataArray, $id_alias)
    {
        $dataArray = array_combine(
            array_map(function ($k) use ($target) {
                return $k.$target;
            }, array_keys($dataArray)),
            $dataArray
        );
        $database->query()
            ->from($table)
            ->where("$id_alias = ?", $id)
            ->save($dataArray);
        $response = new \stdClass();
        $response->type = "success";
        return \json_encode($response);
    }
}