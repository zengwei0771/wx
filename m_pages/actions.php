<?php
    require_once('../pages/db.php');

    if ($_GET['titleid']) {
        if ($_GET['a'] == 'read') {
            $sql = 'update article set `read`=`read`+1 where titleid="'.$_GET['titleid'].'"';
            $r = $db->executeSql($sql);
        } else if ($_GET['a'] == 'agree') {
            $sql = 'update article set `agree`=`agree`+1 where titleid="'.$_GET['titleid'].'"';
            $r = $db->executeSql($sql);
        }
    }

    echo $r;
?>
