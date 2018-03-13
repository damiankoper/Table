<?php

class Plan {

    public $plan;

    function __construct($date = null) {

            $stmt = $GLOBALS['conn']->prepare("SELECT * FROM plan WHERE DATE(date)=? ORDER BY CASE WHEN subplan_assigned = 'AW_001' THEN 1 ELSE 2 END");
            $stmt->bind_param("s", $date);
            if (!$stmt->execute()) {
                exit("Błąd przy wykonywaniu zapytania");
            }
            $this->plan = $stmt->get_result();
        
    }
    
    function renderPlan() {
        $render_html ="";
        $render_html .= "<div class='plan-main'>";

        while ($subplan = $this->plan->fetch_array(MYSQLI_NUM)) {
            if ($subplan[1] == "Inne") {
                continue;
            }
            $opacity_multipler = 0.5;
            $color_rules_section = array(
                "PKF" => "rgba(255, 0, 0," . $opacity_multipler * 1 . ")",
                "PKFO" => "rgba(104, 160, 221," . $opacity_multipler * 1 * 4 . ")",
                "NR" => "rgba(0,200,0," . $opacity_multipler * 1 . ")",
                "SM" => "rgba(252,200,0," . $opacity_multipler * 1 * 2 . ")",
            );
            if ($subplan[1] == "PKFO") {
                $color = $color_rules_section["PKFO"];
            }
            if ($subplan[1] == "PKF") {
                $color = $color_rules_section["PKF"];
            }
            if ($subplan[1] == "Nieruchomości") {
                $color = $color_rules_section["NR"];
            }
            if ($subplan[1] == "Śniadania Mistrzów") {
                $color = $color_rules_section["SM"];
            }
            $checklist_c = new Checklist_comments(array(1, 0), $subplan[0], "plan", "subplan_id");
            $render_html .= "<div class='subplan' data-id='$subplan[0]' data-assigned='$subplan[2]'>";
            $render_html .= "<div class='subplan-title' style='background-color:$color; color:black;'>";
            $render_html .= "<p>$subplan[1]</p><p>$subplan[2]</p>";
            $render_html .= "</div>";
            $render_html .= $checklist_c->renderCL(true, false);
            $render_html .= "</div>";
        }
        $render_html .= "</div>";
        return $render_html;
    }

}
