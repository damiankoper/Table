<?php
namespace Table;

use Tegs\Template as Template;

class Table extends Base
{
    protected $_path;
    protected $_title;
    protected $_query;
    protected $_data;
    protected $_columns;
    protected $_database;
    protected $_attributes = [];
    protected $_filters = [];
    protected $_rowColorRules = [];
    protected $_functions = [];
    protected $_rowsInfo = [];
    protected $_slidableFunction;
    protected $_buttonsTemplate = "";
    protected $_print = false;

   /* protected $_addForm;
    protected $_addFormScope;*/

    protected $_addFormRendered;

    public function __construct($options = null)
    {
        parent::__construct($options);
        $root = \realpath($_SERVER["DOCUMENT_ROOT"]);
        $dir = \realpath(__DIR__);
        $this->_path = \str_replace($root, "", $dir);
    }

    private function _handleAttr($text, $attr)
    {
        switch ($attr) {
            case "settings" :
                $template = new Template(array("_template" => "Table/templates/settings.html.tegs"));
                return $template->render(array(
                    "path" => $this->_path,
                    "buttonsTemplate" => $this->_buttonsTemplate,
                ));
            case "slidable" :
                $template = new Template(array("_template" => "Table/templates/slidable.html.tegs"));
                return $template->render(array("path" => $this->_path));
            case "escapehtml" :
                return \htmlspecialchars($text);
            case "cash" :
                return number_format($text, 2, ',', ' ') . "zł";
            case "bool" :
                return (\intval($text) == true) ? "TAK" : "NIE";
            case "nl2br":
                return \strip_tags(\nl2br($text),"<br>");
        }
        return $text;
    }

    private function _handleFunctions($text, $func)
    {
        if (\function_exists($func)) {
            $text = \call_user_func_array($func, array($text));
        }
        return $text;
    }

    private function rowColorClass($row)
    {
        $class = "";
        if (!empty($this->_rowColorRules)) {
            if (\is_array($row) && \array_key_exists($this->_rowColorRules[0], $row)) {
                foreach ($this->_rowColorRules[1] as $key => $class_f) {
                    if ($row[$this->_rowColorRules[0]] == $key) {
                        $class = $class_f;
                    }
                }
            }
            if ($class === "") {
                $class = $this->_rowColorRules[1]["_default"];
            }
        }

        return $class;
    }

    private function create_table_scope()
    {

        $hidden = [];
        foreach ($this->_attributes as $col => $attr) {
            if (\strpos($attr, "hidden") !== false) {
                $hidden[] = array("class" => \str_replace(" ", "-", mb_strtolower($col)), "name" => $col);
            }
        }

        $filtered_by = [];
        $where = [];
        $array = [];
        if (!empty($_GET["filter"])) {
            $filter = $_GET["filter"];
            foreach ($filter as $filter => $value) {
                $bool = false;
                foreach (\array_keys($this->_columns) as $column) {
                    $bool = (\strpos($column, $filter) !== false || $bool) ? true : false;
                }
                if ($bool) {
                    $where[] = $filter . " = ? ";
                    $array[] = $value;
                    $filtered_by[] = $this->_columns[$filter];
                }
            }
            \array_unshift($array, \implode(" AND ", $where));
            \call_user_func_array(array($this->_query, "where"), $array);
        }

        $this->_data = $this->_query->all();
        $this->_rowsInfo = $this->_query->rowsInfo();
        if (empty($this->_data)) {
            $data = false;
        }
        else {
            $headres = array();
            foreach (\array_keys($this->_data[0]) as $key => $header) {
                $info["text"] = $header;
                $class = "";
                if (\array_key_exists($header, $this->_attributes)) {
                    $class = \str_replace(" ", "-", mb_strtolower($header)) . " " . \mb_strtolower($this->_attributes[$header]);
                }
                else {
                    $class = \str_replace(" ", "-", mb_strtolower($header));
                }
                $info["class"] = $class;
                $headers[] = $info;
            }

            $rows = $this->_data;
            foreach ($rows as &$row) {
                $row_class = $this->RowColorClass($row);
                $slidableFunction = $this->_slidableFunction;
                if ($slidableFunction instanceof \Closure) {
                    $slidable = $slidableFunction($row, $this->_rowsInfo);
                }
                else {
                    $slidable = "0";
                }

                $row = array("slidable" => $slidable, "class" => $row_class, "cells" => $row);

                foreach ($row["cells"] as $key => &$cell) {
                    $class = "";
                    if (\array_key_exists($key, $this->_functions)) {
                        foreach (explode(" ", $this->_functions[$key]) as $func) {
                            $cell = $this->_handleFunctions($cell, $func);
                        }
                    }
                    if (\array_key_exists($key, $this->_attributes)) {
                        foreach (explode(" ", $this->_attributes[$key]) as $attr) {
                            $cell = $this->_handleAttr($cell, $attr);
                        }
                        $class = $this->_attributes[$key];
                    }
                    $row_key = \array_search($key, array_keys($row["cells"]));
                    $cell = array(
                        "text" => $cell,
                        "class" => \str_replace(" ", "-", mb_strtolower($key)) . " " . $class,
                        "orgname" => $this->_rowsInfo[$row_key]->orgname,
                        "table" => $this->_rowsInfo[$row_key]->table
                    );
                }
            }
            $data = true;
        }

        $filters = [];
        foreach ($this->_filters as $name => $col) {
            $options = [];
            if (\is_array($col)) {
                $values = [];
                foreach ($col["values"] as $key => $value) {
                    $values[] = array("val" => $key, "text" => $value);
                }
                $filters[] = array(
                    "name" => $col["name"],
                    "col-class" => \str_replace(".", "-", $col["col"]),
                    "col" => $col["col"],
                    "values" => $values,
                );
            }
            else {
                $values = $this->_database->query()
                    ->from($this->_rowsInfo[0]->table, array(
                    "DISTINCT {$col}"
                ))
                    ->order($col)
                    ->all();
                foreach ($values as &$value) {
                    if (current($value) === "") {
                        $value = array("val" => current($value), "text" => "BRAK");
                    }
                    else {
                        $value = array("val" => current($value), "text" => current($value));
                    }
                }
                $filters[] = array(
                    "name" => $name,
                    "col-class" => \str_replace(".", "-", $col),
                    "col" => $col,
                    "values" => $values
                );
            }
        }

        if (empty($filtered_by)) $filtered_by[] = "Brak";
        return $scope = array(
            "data" => $data,
            "headers" => $headers,
            "rows" => $rows,
            "path" => $this->_path,
            "title" => $this->_title,
            "filters" => $filters,
            "filtered_by" => $filtered_by,
            "hidden" => $hidden,
            "addForm" => $this->_addFormRendered,
            "print"=> $this->_print,
        );
    }

    public function render_table()
    {
        $template = new Template(array("_template" => "Table/templates/table.html.tegs"));
        return $template->render($this->create_table_scope());
    }
    public function render_all()
    {
        $template = new Template(array("_template" => "Table/templates/table_control.html.tegs"));
        return $template->render($this->create_table_scope());
    }
    public function setSlidable($function)
    {
        $this->_slidableFunction = $function;
    }
    public function remove_row($id, $id_alias = "id")
    {
        $response = new \stdClass();
        if ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'))) {
            try {
                $row = $this->_database->query()
                    ->from($this->_table)
                    ->where("$id_alias=?", $id)
                    ->delete();
            } catch (\Exception $e) {
                $response->type = "error";
            }
        }
        if ($row === 1) {
            $response->type = "success";
        }
        else {
            $response->type = "error";
        }
        return json_encode($response);
    }
    public function edit_row($id, $data, $idName = "id")
    {
        $response = new \stdClass();
        if ( (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower(getenv('HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest'))) {
            $save = [];
            $columns = [];
            $this->_data = $this->_query->all();
            $this->_rowsInfo = $this->_query->rowsInfo();
            foreach ($data as $table => $columns) {
                foreach ($columns as &$column) {
                    unset($column["column"], $column["text"]);
                    $item = null;
                    foreach ($this->_rowsInfo as $struct) {
                        if (key($column) == $struct->orgname) {
                            $item = $struct;
                            break;
                        }
                    }
                    if ($item === null) continue;
                    $save[key($column)] = \current($column);
                }

                $row = $this->_database->query()
                    ->from($table)
                    ->where("$idName=?", $id)
                    ->save($save);
            }
        }
        if ($row === 0) {
            $response->type = "success";
        }
        else {
            $response->type = "error";
        }
        return json_encode($response);
    }
    public function setAddForm($template_path, $scope)
    {
        $template = new Template(array("_template" => $template_path));
        $this->_addFormRendered = $template->render($scope);
    }
}
