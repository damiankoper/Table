<?php
namespace KIK;

class KIKAjax extends \Table\Base
{

    public static function updateKIK($database, $table, $id, $dataArray, $id_alias = "id")
    {
        $database->query()
            ->from($table)
            ->where("$id_alias = ?", $id)
            ->save($dataArray);
        $response = new \stdClass();
        $response->type = "success";
        return \json_encode($response);
    }
    public static function splitCash($database, $table, $id, $dataArray)
    {
        $response = new \stdClass();

        if (!\is_numeric($dataArray["amount"])) {
            $response->type = "error";
            return \json_encode($response);
        }


        $database->query()
            ->from($table)
            ->where("id = ?", $id)
            ->save(array("paid" => true, "amount" => $dataArray["amount"]));

        for ($i = 0; $i < 3; $i++) {
            $user = $database->query()
                ->from("accounts", array('to_pay_direct', 'to_pay_indirect', 'to_pay_ever', 'recom'))
                ->where("user_id = ?", $dataArray["user_id"])
                ->all();

            if ($i == 0) {
                $m = 0.2;
                $saveArray = array(
                    'to_pay_direct' => $user[0]['to_pay_direct'] + ($dataArray["amount"] * $m),
                    'to_pay_ever' => $user[0]['to_pay_ever'] + ($dataArray["amount"] * $m)
                );
            }
            else {
                $m = 0.05;
                $saveArray = array(
                    'to_pay_indirect' => $user[0]['to_pay_direct'] + ($dataArray["amount"] * $m),
                    'to_pay_ever' => $user[0]['to_pay_ever'] + ($dataArray["amount"] * $m)
                );
            }

            $database->query()
                ->from("accounts")
                ->where("user_id = ?", $dataArray["user_id"])
                ->save($saveArray);
            $dataArray["user_id"] = $user[0]["recom"];
            if ($user[0]["recom"] == "") break;
        }

        $response->type = "success";
        return \json_encode($response);
    }
}