<?php

$col_name = array("Wypłata");
$col_id = array("awaits_payment");
$users = new Table("accounts", $col_name, $col_id, -1);
$c = $users->addWhereQuery('awaits_payment', 1, 'i')->mainQuery()->getNumRows();
$style="";
if($c!==0) $style = "style='background-color:#ff6363;'";
?>

<div class="admin-bar">
    <a href="index.php">Plan</a>
    <a href="polecani.php">Polecani klienci</a>
    <a href="kontakty.php">Kontakty</a>
    <a href="sprawy.php">Sprawy</a>
    <a href="wnioski.php">Wnioski</a>
    <a href="sm_zapisy.php">Zapisy SM</a>
    <a href="users.php" <?=$style?>>Użytkownicy</a>
    <a href="logout.php">Wyloguj się</a>
    <!--    <a style=" flex:initial;padding:0.4em" href="zmiany.php">Ostatnie<br>zmiany</a>-->
</div>
<div class="alert-bar" style="background-color: rgba(255,0,0,0.25);">
<!--    <p>Nie edytować nowego KIKu</p>       -->
</div>