<?php
    require_once('config.php');
    require_once('db.php');

    $cpp = 40;  //count per page

    function get_articles($catagory, $sortby, $page) {
        global $db, $cpp;
        if ($catagory != 'all') {
            $c = $db->getCountByAtr('article', 'catagoryid', $catagory);
        } else {
            $c = $db->getCountByAtr('article');
        }
        $hasmore = $c > $page*$cpp;

        $sql = 'select *, account.name as account_name, account.desc as account_desc, article.desc as article_desc from article left join account on account_id = aid';
        if ($catagory != 'all') {
            $sql .= ' where catagoryid = "'.$catagory.'"';
        }
        if ($sortby == 'time') {
            $sql .= ' order by time desc';
        } else if ($sortby == 'read') {
            $sql .= ' order by `read` desc';
        } else if ($sortby == 'agree') {
            $sql .= ' order by `agree` desc';
        } else { //agreeratio
            $sql .= ' order by `agree`/`read` desc';
        }
        $start = ($page-1) * $cpp;
        $sql .= ' limit '.$start.', '.$cpp;
        return array($db->getObjListBySql($sql), $hasmore);
    }

    function get_catagorys() {
        global $db;
        $last7day = date("Y-m-d", strtotime("-7 day"));
        $sql = 'select `catagoryid`, `catagory`, count(*) as c from `article` where `time` > "'.$last7day.'" group by `catagoryid`, `catagory` order by c desc';
        return $db->getObjListBySql($sql);
    }

    function get_rand_articles() {
        global $db;
        $lastday = date("Y-m-d", strtotime("-3 day"));
        $sql = 'select * from article where `time` > "'.$lastday.'"';
        $data = $db->getObjListBySql($sql);
        $result = Array();
        if (count($data) > 15) {
            $dist = count($data) / 15;
        } else {
            $dist = 1;
        }
        for($i = $dist; $i < count($data); $i+=$dist) {
            array_push($result, $data[$i]);
        }
        return $result;
    }

    function get_hot_accounts() {
        global $db;
        $last7day = date("Y-m-d", strtotime("-7 day"));
        $sql = 'select aid, `name` as account_name, sum(`read`) as allread, sum(agree) as allagree from article left join account on account_id = aid where `time`>"'.$last7day.'" group by account_id order by allagree desc limit 0, 15';
        return $db->getObjListBySql($sql);
    }


    function get_article($titleid) {
        global $db;
        $sql = 'select *, article.desc as article_desc, account.desc as account_desc, account.name as account_name from article left join account on account_id = aid where titleid="'.$titleid.'"';
        $data = $db->getObjListBySql($sql);
        if (count($data) == 0) {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            include("404.php");
            exit(1);
        }
        return $data[0];
    }

    function get_account_articles($accountid) {
        global $db;
        $sql = 'select * from article where account_id = "'.$accountid.'" order by `time` desc';
        return $db->getObjListBySql($sql);
    }

    function get_more_articles($articleid) {
        global $db;
        $sql = 'select * from article where articleid > "'.$articleid.'" limit 0, 15';
        return $db->getObjListBySql($sql);
    }


    function get_account($accountid, $page) {
        global $db, $cpp;
        $account = $db->getDataByAtr('account', 'aid', $accountid);
        if (count($account) == 0) {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            include("404.php");
            exit();
        }
        $c = $db->getCountByAtr('article', 'account_id', $accountid);
        $hasmore = $c > $page*$cpp;
        $sql = 'select * from article where account_id = "'.$accountid.'" order by `time` desc';
        $start = ($page-1) * $cpp;
        $sql .= ' limit '.$start.', '.$cpp;
        $articles = $db->getObjListBySql($sql);
        return array($account[0], $articles, $hasmore);
    }


    function get_catagory_name_by_id($catagoryid) {
        global $db;
        $article = $db->getDataByAtr('article', 'catagoryid', $catagoryid);
        if (count($article) == 0) {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            include("404.php");
            exit();
        }
        return $article[0]->catagory;
    }

    function get_videos($page) {
        global $db;
        $cpp = 5;
        $sql = 'select count(*) as c from article where `video` != ""';
        $c = $db->getObjListBySql($sql);
	$c = $c[0]->c;
        $hasmore = $c > $page*$cpp;

        $sql = 'select * from article where `video` != "" order by `time` desc';
        $start = ($page-1) * $cpp;
        $sql .= ' limit '.$start.', '.$cpp;
        return array($db->getObjListBySql($sql), $hasmore);
    }

    function get_other_videos() {
        global $db;
        $lastday = date("Y-m-d", strtotime("-3 day"));
        $sql = 'select * from article where `time` > "'.$lastday.'" and `video` != ""';
        $data = $db->getObjListBySql($sql);
        $result = Array();
        if (count($data) > 15) {
            $dist = count($data) / 15;
        } else {
            $dist = 1;
        }
        for($i = $dist; $i < count($data); $i+=$dist) {
            array_push($result, $data[$i]);
        }
        return $result;
    }
?>