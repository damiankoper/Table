<?php
include 'checklist_comments.php';
include_once("tableee.php");
if ($_POST['cl'] !== "false") {
    $col_name = array("ID");
    $col_id = array($_POST['id_column']);
    $table = new Table($_POST["table_id"], $col_name, $col_id);
    if ($_POST['ifcl'] == 1 && $_POST['ifcom'] == 1) {
        $table->updateTable(array($_POST['cl_col'] => $_POST['cl'], 'comments' => $_POST['com']), "sss", $_POST['id']);
    }
    if ($_POST['ifcl'] == 1 && $_POST['ifcom'] == 0) {
        $table->updateTable(array($_POST['cl_col'] => $_POST['cl']), "ss", $_POST['id']);
    }
    if ($_POST['ifcl'] == 0 && $_POST['ifcom'] == 1) {
        $table->updateTable(array('comments' => $_POST['com']), "ss", $_POST['id']);
    }
}
$checklist_i = new Checklist_comments(array($_POST['ifcl'], $_POST['ifcom']), $_POST["id"], $_POST["table_id"], $_POST['id_column'],null,$_POST['cl_col'], $_POST['cl_title'], $_POST['cl_sh_slided']);
echo $checklist_i->renderCL($_POST['reversed'], false);
?>