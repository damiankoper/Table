<?php
namespace Cl_Com;

class Cl_Com extends \Table\Base
{
    protected $_cl;
    protected $_com;
    protected $_AW = false;
    public static function lastCl($data)
    {
        $cl = \json_decode($data);
        if (\is_array($cl) && !empty($cl)) {
            $first = reset(self::sortCL($cl)["now"]);
            if ($first["checked"] !== true) {
                return $first["text"];
            }
        }
        return "-";
    }
    public static function lastCom($data)
    {
        $com = \json_decode($data);
        if (\is_array($com) && !empty($com)) {
            $sorted = [];
            foreach ($com as $item) {
                $sorted[] = (array)$item;
            }
            usort($sorted, array("Cl_Com\Cl_Com", "comComparator"));
            $last = \array_shift($sorted);
            $last["text"]=\htmlspecialchars($last["text"]);
            $date = new \DateTime($last["date"]);
            if ($date->format("Y-m-d") != date("Y-m-d") || $last["date"] == ""){
                return "<div class='last-com'>-</div><div>".$last["text"]."</div>";
            }
            else {
                return $last["text"];
            }
        }

        return "-";
    }
    public function renderCL()
    {
        $template = new \Tegs\Template(array("_template" => "Cl_Com/templates/checklist.html.tegs"));
        $cl = \json_decode($this->_cl);
        $sorted = self::sortCL($cl);
        $scope = array(
            "items" => $sorted,
            "aw"=>$this->_AW
        );
        return $template->render($scope);
    }
    public function renderCom()
    {
        $template = new \Tegs\Template(array("_template" => "Cl_Com/templates/comments.html.tegs"));
        $com = \json_decode($this->_com);
        if (!\is_array($com)) $com = [];
        $sorted = [];
        foreach ($com as $item) {
            $sorted[] = (array)$item;
        }
        usort($sorted, array("Cl_Com\Cl_Com", "comComparator"));
        $scope = array(
            "items" => array_values($sorted),
        );
        return $template->render($scope);
    }

    private static function itemsComparator($a, $b)
    {
        if ($a["checked"] === $b["checked"]) {
            if ($a["no"] < $b["no"]) return true;
        }
        else if ($a["checked"] === true && $b["checked"] !== true) {
            return true;
        }
        return false;
    }
    private static function comComparator($a, $b)
    {
        if (new \DateTime($a["date"]) < new \DateTime($b["date"])) return true;
        return false;
    }
    private static function sortCL($cl)
    {
        $scheduled = [];
        $now = [];
        $today = (new \DateTime())->modify("today");
        if (!\is_array($cl)) $cl = [];
        foreach ($cl as $item) {
            if (!\property_exists($item, "date") || new \DateTime($item->date) <= $today || $item->date == "") {
                if (!\property_exists($item, "date")) {
                    $item->date = "";
                }
                $now[] = (array)$item;
            }
            else {
                $scheduled[] = (array)$item;
            }

        }
        usort($scheduled, array("Cl_Com\Cl_Com", "itemsComparator"));
        usort($now, array("Cl_Com\Cl_Com", "itemsComparator"));
        return array(
            "now" => array_values($now),
            "scheduled" => array_values($scheduled)
        );
    }
}
