<?php

class Checklist_comments {

    public $id;
    public $table_id;
    public $id_column;
    public $checklist;
    public $comments;
    public $filter = [];
    public $title_cl;
    public $cl_col_name;
    public $cl_sh_slided;

    function cmp($a, $b) {
        return $a->no < $b->no;
    }

    function cmp1($a, $b) {
        return $a->no > $b->no;
    }
function cmp2($a, $b) {
        return $a->date < $b->date;
    }
    function __construct($filter, $id = null, $table_id = null, $id_column = null, $content = null, $cl_col_name = "checklist", $title_cl = "Checklista", $cl_sh_slided = false) {
        $this->id = $id;
        $this->table_id = $table_id;
        $this->id_column = $id_column;
        $this->filter = $filter;
        $this->title_cl = $title_cl;
        $this->cl_col_name = $cl_col_name;
        $this->cl_sh_slided = $cl_sh_slided;
        if ($this->filter[0] == 1) {
            if ($this->id != null) {
                $stmt = $GLOBALS['conn']->prepare("SELECT $cl_col_name FROM $this->table_id WHERE $this->id_column=?");
                $stmt->bind_param("s", $this->id);
                if (!$stmt->execute()) {
                    exit("Błąd przy wykonywaniu zapytania");
                }
                $result = $stmt->get_result()->fetch_array(MYSQLI_NUM);
                $this->checklist = $result[0];
            }
        }
        if ($this->filter[1] == 1) {
            if ($this->id != null) {
                $stmt = $GLOBALS['conn']->prepare("SELECT comments FROM $this->table_id WHERE $this->id_column=?");
                $stmt->bind_param("s", $this->id);
                if (!$stmt->execute()) {
                    exit("Błąd przy wykonywaniu zapytania");
                }
                $result = $stmt->get_result()->fetch_array(MYSQLI_NUM);
                $this->comments = $result[0];
            }
        }
        if ($content !== null) {
            $this->checklist = $content[0];
            $this->comments = $content[1];
        }
    }

    function firstUnchecked() {
        if ($this->checklist != '')
            $content_decoded = json_decode($this->checklist);
        else
            return '-';
        usort($content_decoded, array($this, "cmp1"));
        foreach ($content_decoded as $cd) {
            if ($cd->checked === false) {
                return $cd->text;
            }
        }
        return "-";
    }

    function getLastCom() {
        $comments = json_decode($this->comments);
        if ($comments == null) {
            return "-";
        }
        $date = new DateTime(end($comments)->date);
        if ($date->format("Y-m-d") != date("Y-m-d") || end($comments)->date == "")
            return "-<div class='last-com'>".end(json_decode($this->comments))->text."</div>";
        return (end(json_decode($this->comments))->text);
    }

    function separate($checklist) {
        $response = new stdClass();
        $response->now = array();
        $response->scheduled = array();
        $today = (new DateTime())->modify("today");
        $checklist = json_decode($checklist);
        if ($checklist ==null)return $response;
        foreach ($checklist as $item) {
            if ($item->date == "" || new DateTime($item->date) <= $today) {
                array_push($response->now, $item);
            } else {
                array_push($response->scheduled, $item);
            }
        }
        return $response;
    }

    function renderCL($reversed, $additional_info) {
        $render_html = "";
        $render_html .= "<div class='cl_com_container'>";
        $render_html .= "<h2 style='float:left;' class='loading_text zeroopacity'>Ładowanie...</h2>";
        $render_html .= "<button onclick=\"$(this).next().next().trigger('info_slided');\"  class='button-1 refresh'><img style='height:100%;' src='Table/img/reload.png'/></button>";
        $render_html .= "<button title='Rozwiń' onclick=\"$(this).children().toggleClass('z-rotated').parent().parent().find('.cl_com.cl > :last-child').slideToggle();\" style='float:right;padding:0.25em;margin:0.5em;width:3em;height:3em; background-color:#FFAB68; border-color:#FFAB68;' class='button-1 nomarginright'><img class=\"";if($this->cl_sh_slided){$render_html.="z-rotated";} $render_html.="\"style='width:100%;height:100%;' src='Table/img/down-arrow.png'/></button>";
        $render_html .= "<div class='cl_com_main' id='cl_com-$this->id' data-cl_title='$this->title_cl' data-cl_col='$this->cl_col_name' data-reversed='$reversed' data-cl='" . $this->filter[0] . "' data-com='" . $this->filter[1] . "' data-id='$this->id' data-table-id='$this->table_id' data-col-id='$this->id_column'>";
        if ($this->filter[0] == 1) {
            $render_html .= "<div class='cl_com cl'><div><div class='header'><h2>$this->title_cl</h2>"
                    . "<button title='Dodaj' onclick=\"clAdd($(this).parent());\" class='button-1 add_button'><img style='width:100%;height:100%;' src='Table/img/add_white.png'/></button></div>";
            //$checklist_decoded = (json_decode($this->checklist) == null) ? array() : json_decode($this->checklist);
            $checklist_sep = $this->separate($this->checklist);
             if ($reversed != true) {
                    usort($checklist_sep->now, array($this, "cmp1"));
                    usort($checklist_sep->scheduled, array($this, "cmp1"));
                } else {
                    usort($checklist_sep->now, array($this, "cmp"));
                    usort($checklist_sep->scheduled, array($this, "cmp"));
                }
            if ($checklist_sep->now == array()) {
                $render_html .= "<div  class='filler'>
                        <label for=''>
                            Brak punktów
                        </label>
                    </div>";
            } else {
                $i = rand(0, 99999999);
               
                //print_r($checklist_decoded);
                $unchecked = "";
                //$render_html .= var_dump($checklist_decoded, true);
                foreach ($checklist_sep->now as $cl) {
                    if ($cl->checked === false) {
                        $render_html .= "<div title='$cl->date'>
                        <label for='cl$i'>
                            <div>
                                <input type='checkbox' name='$cl->no' data-date='$cl->date'";
                        if ($cl->checked === true) {
                            $render_html .= "checked";
                        }
                        $render_html .= " id='cl$i'>
                            </div>
                            <div class='cl_com_text'>" . $cl->text . "</div>
                        </label>
                        <button title='Edytuj' onclick=\"clEdit($(this));\" class='button-1'><img style='width:100%;height:100%;' src='Table/img/edit.png'/></button>
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>
                    </div>";
                        $i = rand(0, 99999999);
                    } else {
                        $unchecked .= "<div title='$cl->date'>
                        <label for='cl$i'>
                            <div>
                                <input type='checkbox' name='$cl->no' data-date='$cl->date'";
                        if ($cl->checked === true) {
                            $unchecked .= "checked";
                        }
                        $unchecked .= " id='cl$i'>
                            </div>
                            <div class='cl_com_text'>" . $cl->text . "</div>
                        </label>
                        <button title='Edytuj' onclick=\"clEdit($(this));\" class='button-1'><img style='width:100%;height:100%;' src='Table/img/edit.png'/></button>
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>
                    </div>";
                        $i = rand(0, 99999999);
                    }
                }
                $render_html .= $unchecked;
            }
            
            if($this->cl_sh_slided)$style_sh='display:block;';
            else $style_sh = 'display:none;';
            
            $render_html .= "</div><div style='$style_sh' class='cl_scheduled'><div class='header'><h2>PLANOWA $this->title_cl</h2>"
                    . "<button title='Dodaj' onclick=\"clAdd($(this).parent());\" class='button-1 add_button'><img style='width:100%;height:100%;' src='Table/img/add_white.png'/></button></div>";
            //$checklist_decoded = (json_decode($this->checklist) == null) ? array() : json_decode($this->checklist);
            if ($checklist_sep->scheduled == array()) {
                $render_html .= "<div  class='filler' >
                        <label for=''>
                            Brak punktów
                        </label>
                    </div>";
            } else {
                $i = rand(0, 99999999);
               
                //print_r($checklist_decoded);
                $unchecked = "";
                //$render_html .= var_dump($checklist_decoded, true);
                foreach ($checklist_sep->scheduled as $cl) {
                    if ($cl->checked === false) {
                        $render_html .= "<div title='$cl->date'>
                        <label for='cl$i'>
                            <div>
                                <input type='checkbox' name='$cl->no'  data-date='$cl->date'";
                        if ($cl->checked === true) {
                            $render_html .= "checked";
                        }
                        $render_html .= " id='cl$i'>
                            </div>
                            <div class='cl_com_text'>" . $cl->text . "</div>
                        </label>
                        <button title='Edytuj' onclick=\"clEdit($(this));\" class='button-1'><img style='width:100%;height:100%;' src='Table/img/edit.png'/></button>
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>
                    </div>";
                        $i = rand(0, 99999999);
                    } else {
                        $unchecked .= "<div title='$cl->date'>
                        <label for='cl$i'>
                            <div>
                                <input type='checkbox' name='$cl->no' data-date='$cl->date'";
                        if ($cl->checked === true) {
                            $unchecked .= "checked";
                        }
                        $unchecked .= " id='cl$i'>
                            </div>
                            <div class='cl_com_text'>" . $cl->text . "</div>
                        </label>
                        <button title='Edytuj' onclick=\"clEdit($(this));\" class='button-1'><img style='width:100%;height:100%;' src=Table/img/edit.png'/></button>
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>
                    </div>";
                        $i = rand(0, 99999999);
                    }
                }
                $render_html .= $unchecked;
            }
            $render_html .= "</div></div>";
        }
        if ($this->filter[1] == 1) {
            $render_html .= "<div class='cl_com com'><div class='header'><h2>Komentarze</h2>"
                    . "<button title='Dodaj' onclick=\"comAdd($(this).parent());\" class='button-1 add_button'><img style='width:100%;height:100%;' src='Table/img/add_white.png'/></button></div>";
            $checklist_decoded = (json_decode($this->comments) == null) ? array() : json_decode($this->comments);
            if ($checklist_decoded == array()) {
                $render_html .= "<div class='filler'>
                        <label class='removed ' for=''>
                            Brak komentarzy
                        </label>
                    </div>";
            }
            $i = 0;
            if ($reversed == true) {
                usort($checklist_decoded, array($this, "cmp2"));
            }
            foreach ($checklist_decoded as $com) {
                $render_html .= "<div>
                        <label for=''>
                            <div class='cl_com_text'>" . $com->text . "</div>
                                <div class='com_date'>" . $com->date . "</div>
                        </label>
                        <button title='Edytuj' onclick=\"comEdit($(this));\" class='button-1'><img style='width:100%;height:100%;' src=\"Table/img/edit.png\"/></button>
                        <button title='Zapisz' onclick=\"clSubmit($(this));\" class='button-1 hide'><img style='width:100%;height:100%;' src='Table/img/success.png'/></button>
                        <button title='Usuń' onclick=\"clRemove($(this));\" class='button-1 nomarginright'><img style='width:100%;height:100%;' src='Table/img/remove.png'/></button>
                    </div>";
                $i++;
            }

            $render_html .= "</div>";
        }
        $render_html .= "</div></div>";
        return $render_html;
    }

}
