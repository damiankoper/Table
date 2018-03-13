<?php

include "connect.php";

//Specs
# Na pierwszym miejscu w col_id musi być identyfikator rekordu ..._id
# renderTable może mnieć parametr $custom_html - jeden rekord, gdzie %r'nrkolumny'% będzie zamieniany na dane


class Table {

    public $table_id;
    public $table_joined_id;
    public $col_name = [];
    public $col_id = [];
    public $col_joined_name = [];
    public $col_joined_id = [];
    public $sort_col = false;
    public $sort_dir = "up";
    public $edit = false;
    public $editable = [];
    public $nowrap = [];
    public $extraButtons = [];
    public $slidable_info = [];
    public $slidable_info_buttons = [];
    public $hide_id = false;
    public $hide = [];
    public $hide_ever = [];
    public $select = [];
    public $join_condition_id;
    public $join_where;
    public $join_type;
    public $sorting_query = "";
    public $where_query = [];
    public $query_result;
    public $cash = [];
    public $bool = [];
    public $custom_column = [];
    public $custom_column_name = [];
    public $custom_column_where = [];
    public $custom_column_params = [];
    public $elipse = [];
    public $color_filter_column;
    public $color_filter_rules = [];
    public $text_color_filter_column;
    public $text_color_filter_rules = [];

    function addButton($html) {
        array_push($this->extraButtons, $html);
        return $this;
    }

    function addSelect($i, $data) {
        $obj = new stdClass();
        $obj->i = $i;
        $obj->data = $data;
        array_push($this->select, $obj);
        return $this;
    }

    function getQuery() {
        $this->mainQuery();
        $return = array();
        while ($row = $this->query_result->fetch_array(MYSQLI_NUM)) {
            array_push($return, $row);
        }
        return $return;
    }

    function joinTable(Table $table, $condition_id, $where, $type = "INNER") {
        $this->join_condition_id = $condition_id;
        $this->join_where = $where;
        $this->join_type = $type;
        $this->col_joined_id = $table->col_id;
        $this->col_joined_name = $table->col_name;
        $this->table_joined_id = $table->table_id;

        if (!empty($this->col_joined_id)) {
            for ($i = 0; $i < count($this->join_where); $i++) {
                array_splice($this->col_id, $this->join_where[$i], 0, $this->col_joined_id[$i]);
            }
        }
        return $this;
    }

    function addSortingQuery($query) {
        $this->sorting_query = $query;
        return $this;
    }

    function addWhereQuery($col, $value, $type, $connector = "") {
        //$this->where_query = [];
        array_push($this->where_query, array($col, $value, $type, $connector));
        return $this;
    }

    function removeAllWhereQuery() {
        $this->where_query = [];
        return $this;
    }

    function addInfoColumn($html) {
        array_push($this->extraButtons, $html);
        return $this;
    }

    function addSlidableInfo($html, $name) {
        array_push($this->slidable_info, $html);
        array_push($this->slidable_info_buttons, $name);
        return $this;
    }

    function addCustomColumn($function, $name, $where, $params = null) {
        array_push($this->custom_column, $function);
        array_push($this->custom_column_name, $name);

        array_push($this->custom_column_where, $where);
        array_push($this->custom_column_params, $params);

        return $this;
    }

    function setRowColorByRule($filter_column, $rules) {
        print_r($rules, true);
        $this->color_filter_column = $filter_column;
        $this->color_filter_rules = $rules;
        return $this;
    }

    function setRowTextColorByRule($filter_column, $rules) {
        print_r($rules, true);
        $this->color_filter_column = $filter_column;
        $this->text_color_filter_rules = $rules;
        return $this;
    }

    private function setRowColor($valuee) {
        $color = 'initial';
        foreach ($this->color_filter_rules as $key => $value) {
            if ($key == $valuee) {
                $color = $value;
                break;
            }
        }
        $text_color = 'initial';
        foreach ($this->text_color_filter_rules as $key => $value) {
            if ($key == $valuee) {
                $text_color = $value;
                break;
            }
        }
        return "style='background-color:$color; color:$text_color'";
    }

    function setCash($array) {
        $this->cash = $array;
        return $this;
    }

    function setBool($array) {
        $this->bool = $array;
        return $this;
    }

    function getTabID() {
        return $this->table_id;
    }

    function getResultRow() {
        return $this->query_result->fetch_array(MYSQLI_NUM);
    }

    function getNumRows() {
        if (empty($this->query_result)) {
            exit("Nie wykonano zapytania do bazy. Użyj mainQuery()!");
        }
        return $this->query_result->num_rows;
    }

    function __construct($table_id, $col_name, $col_id, $sort_col = null, $sort_dir = null, $editable = null, $nowrap = null, $hide_id = null, $hide = null, $elipse = null, $hide_ever = null) {
        $this->table_id = $table_id;
        $this->col_name = $col_name;
        $this->col_id = $col_id;
        $this->col_id = preg_filter('/^/', "$this->table_id.", $this->col_id);
        if ($sort_col != null) {
            $this->sort_col = $sort_col;
        }
        if ($sort_dir != null) {
            $this->sort_dir = $sort_dir;
        }
        if ($editable != null) {
            $this->editable = $editable;
        }
        if ($nowrap != null) {
            $this->nowrap = $nowrap;
        }
        if ($hide_id != null) {
            $this->hide_id = $hide_id;
        }
        if ($hide != null) {
            $this->hide = $hide;
        }
        if ($hide_ever != null) {
            $this->hide_ever = $hide_ever;
        }
        if ($elipse != null) {
            $this->elipse = $elipse;
        }
        return $this;
    }

    function mainQuery() {
        if (!empty($this->col_joined_id)) {
            $join_string = "$this->join_type JOIN $this->table_joined_id ON $this->table_id.$this->join_condition_id=$this->table_joined_id.$this->join_condition_id";
        } else {
            $join_string = "";
        }
        $col_id_string = implode(", ", $this->col_id);
        $where_string = "";
        if (!empty($this->where_query)) {
            $where_string = "WHERE ";
            $where_type = "";
            foreach ($this->where_query as $where_row) {
                $where_string .= $where_row[0] . "=? " . $where_row[3] . " ";
                $where_type .= $where_row[2];
            }
        }
        //print_r("SELECT $col_id_string FROM $this->table_id $join_string $where_string $this->sorting_query");
        $stmt = $GLOBALS['conn']->prepare("SELECT $col_id_string FROM $this->table_id $join_string $where_string $this->sorting_query");
        if (!empty($this->where_query)) {
            $params[0] = $where_type;
            $i = 1;
            foreach ($this->where_query as &$value) {
                $params[$i] = &$value[1];
                $i++;
            }
            //print_r($params);
            call_user_func_array(array($stmt, 'bind_param'), $params);
        }
        if (!$stmt->execute()) {
            exit("Błąd przy wykonywaniu zapytania");
        }
        $this->query_result = $stmt->get_result();
        return $this;
    }

    function renderTable($custom_html = null) {

        $render_html = "";

        if (empty($this->query_result)) {
            exit("Nie wykonano zapytania do bazy. Użyj mainQuery()!");
        }

        if ($custom_html === null) {
            $render_html .= "<table id='$this->table_id' data-more='" . count($this->slidable_info) . "' data-sort-col='$this->sort_col' data-sort-dir='$this->sort_dir' class='table_single'>";
            if ($this->col_name !== array()) {
                $render_html .= "<thead>";
                $col_name_table = $this->col_name;
                for ($i = 0; $i < count($this->join_where); $i++) {
                    array_splice($col_name_table, $this->join_where[$i], 0, $this->col_joined_name[$i]);
                }
                for ($i = 0; $i < count($col_name_table); $i++) {
                    $class = "";
                    if (($this->hide_id && $i === 0) || array_search($i, $this->hide) !== false) {
                        $class .= "hide ";
                    }
                    if (array_search($i, $this->hide_ever) !== false) {
                        $class .= "hide_ever ";
                    }
                    $render_html .= "<th class='$class' onclick='tableSort($(this));'>" . $col_name_table[$i] . "</th>";

                    if (count($this->custom_column) != 0) {
                        for ($j = 0; $j < count($this->custom_column); $j++) {
                            if ($this->custom_column_where[$j] == $i) {
                                $render_html .= "<th onclick='tableSort($(this));'>" . $this->custom_column_name[$j] . "</th>";
                            }
                        }
                    }
                }
                if (count($this->slidable_info_buttons) != 0) {
                    foreach ($this->slidable_info_buttons as $button) {
                        $render_html .= "<th>$button</th>";
                    }
                }
                if ($this->editable != null) {
                    $render_html .= "<th>E/U</th>";
                }

                $render_html .= "</thead>";
            }
            $render_html .= "<tbody>";
            $nth = -1;
            while ($row = $this->query_result->fetch_array(MYSQLI_NUM)) {
                $nth++;
                $render_html .= "<tr " . $this->setRowColor($row[$this->color_filter_column]) . " class='main'>";
                for ($i = 0; $i < count($row); $i++) {
                    $content = nl2br($row[$i]);
                    $class = explode('.', $this->col_id[$i])[1] . " ";
                    if (array_search($i, $this->editable) !== false) {
                        $class = $class . "editable ";
                    }
                    if (array_search($i, $this->nowrap) !== false) {
                        $class = $class . "nowrap ";
                    }
                    if (array_search($i, $this->elipse) !== false) {
                        $class = $class . "elipse ";
                        if ($content == "") {
                            $class = $class . "elipseempty ";
                        }
                    }
                    if (array_search($i, $this->cash) !== false) {
                        $content = number_format($content, 2, ',', ' ') . "zł";
                        $class = $class . "tright ";
                    }
                    if (array_search($i, $this->bool) !== false) {
                        $content = ($content != "0") ? "TAK" : "NIE";
                        $class = $class . "tright ";
                    }

                    if (strpos(explode('.', $this->col_id[$i])[1], 'phone') !== false && $row[$i] != "") {
                        $content = '<a href=tel:+48' . nl2br($row[$i]) . '>' . nl2br($row[$i]) . '</a>';
                        $class .= "tel_link ";
                    }
                    if (($this->hide_id && $i === 0) || array_search($i, $this->hide) !== false) {
                        $class .= 'hide ';
                    }
                    if (array_search($i, $this->hide_ever) !== false) {
                        $class .= 'hide_ever ';
                    }

                    if (count($this->select) != 0) {
                        for ($j = 0; $j < count($this->select); $j++) {
                            if ($this->select[$j]->i == $i) {
                                $content_tmp="";
                                $content_tmp.="<select disabled>";
                                         $content_tmp.="<option value='null' $select>BRAK</option>";
                                    //if($this->select[$j]->data ==null)continue;    
                                    foreach($this->select[$j]->data as $row_s){
                                        if($content==$row_s[0])$select = "selected";
                                        else $select=""; 
                                        $content_tmp.="<option value='$row_s[0]' $select>";
                                        if($row_s[2]=="") $row_s[2]="-----------";
                                        foreach($row_s as &$item){
                                            $item  = strlen($item) > 20 ? substr($item,0,20)."..." : $item;
                                        }
                                        $content_tmp.= implode(" | ", array_reverse(array_slice($row_s, 1,2)));
                                        
                                        $content_tmp.="</option>";   
                                    }    
                                       
                                        
                                $content_tmp.="</select>";
                                $content = $content_tmp;
                                $class.="select ";
                            }
                            
                        }
                    }

                    $render_html .= "<td id=" . explode('.', $this->col_id[$i])[1] . " class='$class'><div class='wrapper'><div class='wrapper2'>" . $content . "</div></div></td>";

                    if (count($this->custom_column) != 0) {
                        for ($j = 0; $j < count($this->custom_column); $j++) {
                            if ($this->custom_column_where[$j] == $i) {
                                if ($this->custom_column_params[$j] !== null) {
                                    if (array_search('cash', $this->custom_column_params[$j]) !== false) {
                                        $content_cc = number_format($this->custom_column[$j]($row, $this->table_id, $this->col_id, $this->query_result, $nth), 2, ',', ' ') . "zł";
                                        $class_cc = $class_cc . "tright ";
                                    } elseif (array_search('digits', $this->custom_column_params[$j]) !== false) {
                                        $content_cc = $this->custom_column[$j]($row, $this->table_id, $this->col_id, $this->query_result, $nth);
                                       $class_cc = $class_cc . "digits ";
                                    }else {
                                        $content_cc = $this->custom_column[$j]($row, $this->table_id, $this->col_id, $this->query_result, $nth);
                                    }
                                } else {
                                    $content_cc = $this->custom_column[$j]($row, $this->table_id, $this->col_id, $this->query_result, $nth);
                                }
                                $render_html .= "<td id='custom_column$i' class='$class_cc'><div class='wrapper'><div class='wrapper2'>" . $content_cc . "</div></div></td>";
                            }
                        }
                    }
                }

                if (count($this->slidable_info) != 0) {
                    $i = -1;
                    foreach ($this->slidable_info as $button) {
                        $i++;
                        $render_html .= "<td class='sm_buttons more_info'><button title='Rozwiń' onclick=\"if(!$(this).parents('tr').nextAll().eq($i).is(':visible'))scrollToObject($(this));$(this).parents('tr').nextAll().eq($i).slideToggle(100).trigger('info_slided');$(this).parents('tr').toggleClass('slided');$(this).children().toggleClass('z-rotated');\" class='button-1 nomargin'><img style='width:100%;height:100%;' src='icons/down-arrow-white.png'/></button></td>";
                    }
                }

                if ($this->editable != null) {
                    $render_html .= "<td class='sm_buttons'>
                    <div id='edit_remove'>
                        <button title='Edytuj' onclick=\"edit($(this));\" class='button-1'><img style='width:100%;height:100%;' src='icons/edit.png'/></button>";

                    foreach ($this->extraButtons as $html) {
                        $render_html .= $html;
                    }

                    $render_html .= "
                        <button title='Usuń' onclick=\"row_remove($(this), " . $row[0] . ");\" class='button-1'><img style='width:100%;height:100%;' src='icons/remove.png'/></button>
                    </div>
                    <div id='ok_cancel' style='display:none;'>
                        <button title='Usuń' onclick=\"edit_submit($(this));\" class='button-1'><img style='width:100%;height:100%;' src='icons/success.png'/></button>
                        <button title='Usuń' onclick=\"edit_exit($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='icons/error.png'/></button>
                    </div></td>";
                }
                $render_html .= "</tr>";
                $i = -1;
                foreach ($this->slidable_info as $slidable_gen_function) {
                    $i++;
                    $render_html .= "<tr id='" . $i . "_slidable_info_row_" . $row[0] . "'class='slidable_info hide'><td colspan='100'>";
                    $render_html .= $slidable_gen_function($row, $this->table_id, $this->col_id);
                    $render_html .= "</td></tr>";
                }
            }
            $render_html .= "</tbody></table>";
        } else {
            while ($row = $this->query_result->fetch_array(MYSQLI_NUM)) {
                preg_match('/%[\w]*%/', $custom_html, $match);
                foreach ($match as $value) {
                    $custom_html_row = str_replace($value, nl2br($row[intval(substr($value, 2, -1))]), $custom_html);
                }
                $render_html .= $custom_html_row;
            }
        }
        return $render_html;
    }

    function updateTable($values, $types, $id) {
        $values_keys = implode('=?, ', array_keys($values));
        //echo "UPDATE $this->table_id SET $values_keys=? WHERE " . $this->col_id[0] . "=?";
        $stmt = $GLOBALS['conn']->prepare("UPDATE $this->table_id SET $values_keys=? WHERE " . $this->col_id[0] . "=?");
        $params[0] = $types;
        $i = 1;
        foreach ($values as $key => &$value) {
            $params[$i] = &$value;
            $i++;
        }

        $params[$i] = &$id;
        call_user_func_array(array($stmt, 'bind_param'), $params);
        $stmt->execute();
        return "<p>Edycja przebiegła pomyślnie - $this->table_id</p>";
    }

    function removeFromTable($id) {
        $stmt = $GLOBALS['conn']->prepare("DELETE FROM $this->table_id WHERE " . $this->col_id[0] . "=?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return "<p>Usuwanie przebiegło pomyślnie - $this->table_id</p>";
    }

    function insertIntoTable($values, $types) {
        $values_keys = implode(', ', array_keys($values));
        $q_marks = str_repeat('?, ', count($values) - 1);
        $stmt = $GLOBALS['conn']->prepare("INSERT INTO $this->table_id($values_keys) VALUES ($q_marks?)");
        $params[0] = $types;
        $i = 1;
        foreach ($values as $key => &$value) {
            $params[$i] = &$value;
            $i++;
        }
        call_user_func_array(array($stmt, 'bind_param'), $params);
        $stmt->execute();
        return "<p>Dodawanie przebiegło pomyślnie - $this->table_id</p>";
    }

    function generateUniqueId($count) {
        while (1) {
            $id = "";
            for ($i = 0; $i < $count; $i++) {
                $id .= rand(0, 9);
            }
            $stmt = $GLOBALS['conn']->prepare("SELECT * FROM $this->table_id WHERE " . $this->col_id[0] . "=?");
            $stmt->bind_param("s", $id);
            if (!$stmt->execute())
                exit("ERRORR");
            $stmt->store_result();
            if ($stmt->num_rows < 1)
                break;
        }
        return $id;
    }

}

?>