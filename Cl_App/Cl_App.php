<?php
namespace Cl_App;

class Cl_App extends \Table\Base
{

    protected $_cl = array(
        ["text"=>"Podziekowanie za R", "color"=>"green"],
        ["text"=>"@ Powitalny", "color"=>"green"],
        ["text"=>"Wniosek", "color"=>"green"],
        ["text"=>"Dok toż", "color"=>"green"],
        ["text"=>"BIG", "color"=>"green"],
        ["text"=>"KRD", "color"=>"green"],
        ["text"=>"Komplet Dok (DB)", "color"=>"green"],
        ["text"=>"Analiza Dok", "color"=>"blue"],
        ["text"=>"Dodatkowe Dok", "color"=>"green"],
        ["text"=>"Oferty", "color"=>"blue"],
        ["text"=>"Umowa PKFO", "color"=>"blue"],
        ["text"=>"Zgoda ANG", "color"=>"blue"],
        ["text"=>"Wniosek", "color"=>"blue"],
        ["text"=>"Wskanowanie Dok (DB)", "color"=>"blue"],
        ["text"=>"Wniosek do banku", "color"=>"blue"],
        ["text"=>"Decyzja", "color"=>"blue"],
        ["text"=>"@ z Decyzją", "color"=>"blue"],
        ["text"=>"Czytanie umowy", "color"=>"blue"],
        ["text"=>"Podpis umowy w banku", "color"=>"blue"],
        ["text"=>"Uruchomienie", "color"=>"blue"],
        ["text"=>"Opinia o PKFO", "color"=>"blue"],
        ["text"=>"CRM PKF", "color"=>"blue"],
        ["text"=>"CRM ANG", "color"=>"green"],
        ["text"=>"F PKFO", "color"=>"blue"],
        ["text"=>"F ANG", "color"=>"blue"],
        ["text"=>"PLN za R", "color"=>"blue"],
        ["text"=>"Upominek", "color"=>"blue"],
    );
    protected $_clData;

    public static function lastNotDone($data){

        $data = \json_decode($data);
        if(!\is_array($data)) return "Niezgodny format danych";
        $data = \array_reverse($data);
        $iterator = 0;
        $array = new self();
        foreach($data as $item){
            if($item->checked===true){
                if($iterator===0)return "Zakończony";
                return \array_reverse($array->_cl)[$iterator-1]["text"];
            }
            $iterator++;
        }
        return $array->_cl[0]["text"];
    }

    public function renderCL()
    {
        $template = new \Tegs\Template(array("_template" => "Cl_App/templates/checklist.html.tegs"));
        $iterator = 0;
        foreach($this->_cl as &$item){
            $item["id"] = $iterator."_pr";
            $item["number"] = ($iterator+1);
            $item["checked"]="";
            $item["textarea"]="";
            if(!empty($this->_clData) && \is_array($this->_clData)){
                if($this->_clData[$iterator]->checked)
                    $item["checked"]="checked";
                $item["textarea"]=$this->_clData[$iterator]->text;
            }
            $iterator++;
        }
        $scope = array(
            "items" => $this->_cl,
        );
        return $template->render($scope);
    }
}
