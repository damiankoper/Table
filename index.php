<?php 
require_once ("Table_autoloader.php");
include 'admin_php_old/table.php';
include 'admin_php_old/checklist_comments.php';
include 'admin_php_old/plan.php';
$database = new Database\Database($options);
$database = $database->init()->connect();

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

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

if (empty($_GET['date'])) {
    $main_date = date("Y-m-d", strtotime("Monday this week"));
}
else {
    $main_date = $_GET['date'];
}
if (!validateDate($main_date, 'Y-m-d')) exit("Błąd: nieprawidłowy link(data).");
$plan = new Plan($main_date);
$h_date = new DateTime($main_date);
$h_date_plus = $h_date;
$h_date_plus->modify("+1 weeks");

$template = new Tegs\Template(array("_template" => "admin_templates/plan.html.tegs"));

$awaits = $database->query()
    ->from("accounts")
    ->where("awaits_payment = ?", 1)
    ->count();
$insert = "";
$plan_dates = new Table('plan', array(''), array('date'));
$plan_dates->addSortingQuery('GROUP BY CAST(date as DATE) ORDER BY date DESC LIMIT 9')->mainQuery();
while ($result = $plan_dates->getResultRow()) {
    $date = new DateTime(substr($result[0], 0, 10));
    $date->modify('+1 week');
    $insert .= '<option data-date=' . substr($result[0], 0, 10) . '>' . substr($result[0], 0, 10) . ' do ' . $date->format('Y-m-d') . '</option>';
}

echo $template->render(array(
    "user_alert" => $awaits > 0,
    "table" =>
        "<h1 style='margin:0.5em;'>Plan $main_date  do " . $h_date_plus->format("Y-m-d") . "</h1>" .
        $plan->renderPlan(),
        "next_sm"=>$next,
    "u_bar" => "
     <div class='u_bar'>
                <div class='filter-div'>
                    Tydzień:
                    <select onchange=\"window.location.search = '&date=' + $(this).children().eq(this.selectedIndex).data('date');\" id='filter_date'>
                        $insert
                    </select>
                    <script>
                        $('#filter_date').children().each(function () {
                            if ($(this).data('date') == '$main_date')
                                $(this).prop('selected', true);
                        });
                    </script>
                    <button onclick=\"filter.filterCustomByOne($('.subplan'), 'assigned', $(this).text());\" class='button-1' type='button'>Albert Ligman</button>
                    <button onclick=\"filter.filterCustomByOne($('.subplan'), 'assigned', $(this).text());\" class='button-1' type='button'>Krzysztof Iwanow</button>
                    <button onclick=\"filter.filterCustomByOne($('.subplan'), 'assigned', $(this).text());\" class='button-1' type='button'>AW_001</button>
                    <button onclick=\"$('.subplan').show();\" class='button-1' type='button'>Wszystkie</button>
                </div>
            </div>
             <script>
            var filter = new Filter();
            filter.autoOff();
            //filter.filterCustomByOne($('.subplan'), 'assigned', 'Krzysztof Iwanow');
        </script>
    ",
    "title"=>"PKFO Admin - Plan"
));
