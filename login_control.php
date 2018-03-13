        <?php
        $dates = $database->query()
            ->from("sm_signups_dates", array("date"))
            ->order("sm_signups_dates.date", "ASC")->all();
        $filter_dates = [];
        $next = strtotime(date("Y-m-d"));
        foreach ($dates as $date) {
            $filter_dates[$date["date"]] = $date["date"];
            if ($next <= strtotime($date["date"]) && $next == strtotime(date("Y-m-d"))) {
                $next = strtotime($date["date"]);
            }
        }
        $next = date("Y-m-d", $next);

        if (empty($_SESSION['admin_session'])) {
            header("Location: login.php?u=http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        }
        $template = new Tegs\Template(array("_template" => "admin_templates/polecani.html.tegs"));

        $awaits = $database->query()
            ->from("accounts")
            ->where("awaits_payment = ?", 1)
            ->count();

        echo $template->render(array(
            "user_alert" => $awaits > 0,
            "table" => $table->render_all(),
            "next_sm"=>$next,
            "title"=>$title
        ));