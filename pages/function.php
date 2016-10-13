<?php
    require_once('config.php');
    require_once('db.php');


    $redis = new Redis();
    $redis->connect('localhost', 6379);
    $redis->select(1);


    function get_articles($catagory, $sortby, $page, $cpp=40) {
        global $db, $redis;
        $c = $redis->get('COUNT:'.strtoupper($catagory));
        if (!$c) {
            if ($catagory != 'all') {
                $c = $db->getCountByAtr('article', 'catagoryid', $catagory);
            } else {
                $c = $db->getCountByAtr('article');
            }
        }
        $hasmore = $c > $page*$cpp;

        $start = ($page-1) * $cpp;
        $end = $start + $cpp - 1;

        if ($page < 100) {
            if ($catagory != 'all') {
                $l = $redis->lRange('LIST:'.strtoupper($catagory), $start, $end);
            } else if ($sortby == 'hot') {
                $l = $redis->lRange('LIST:HOT', $start, $end);
            } else if ($sortby == 'read') {
                $l = $redis->lRange('LIST:HOTREAD', $start, $end);
            } else if ($sortby == 'agree') {
                $l = $redis->lRange('LIST:HOTAGREE', $start, $end);
            } else {
                $l = $redis->lRange('LIST:NEWEST', $start, $end);
            }
            if ($l) {
                $data = array();
                foreach ($l as $id) {
                    $article = $redis->get('ARTICLE:'.$id);
                    array_push($data, json_decode($article));
                }
                return array($data, $hasmore);
            }
        }

        $sql = 'select *, `desc` as article_desc from article';
        if ($catagory != 'all') {
            $sql .= ' where catagoryid = "'.$catagory.'"';
        }
        $sql .= ' order by `date` desc';
        if ($sortby == 'hot') {
            $sql .= ', `index` desc';
        } else if ($sortby == 'read') {
            $sql .= ', `read` desc';
        } else if ($sortby == 'agree') {
            $sql .= ', `agree` desc';
        } else if ($sortby == 'agreeratio') {
            $sql .= ', `agree`/`read` desc';
        }
        $sql .= ' limit '.$start.', '.$cpp;
        $sql = 'select *, account.desc as account_desc from ('.$sql.') t left join account on t.account_id = account.aid';
        return array($db->getObjListBySql($sql), $hasmore);
    }

    function search_articles($k, $page, $cpp=40) {
        global $db;
        $sql = 'select count(*) as c from article where title like "%'.$k.'%"';
        $c = $db->getObjListBySql($sql);
        $c = $c[0]->c;
        $hasmore = $c > $page*$cpp;

        $sql = 'select *, `desc` as article_desc from article where title like "%'.$k.'%" order by `date` desc, `index` desc';
        $start = ($page-1) * $cpp;
        $sql .= ' limit '.$start.', '.$cpp;
        $sql = 'select *, account.desc as account_desc from ('.$sql.') t left join account on t.account_id = account.aid';
        return array($db->getObjListBySql($sql), $hasmore);
    }

    function get_catagorys() {
        global $db, $redis;
        $catagorys = $redis->get('CATAGORYS');
        if ($catagorys) {
            return json_decode($catagorys);
        } else {
            $last7day = date("Y-m-d", strtotime("-7 day"));
            $sql = 'select `catagoryid`, `catagory`, sum(`index`) as hot from `article` where `date` > "'.$last7day.'" group by `catagoryid`, `catagory` order by hot desc';
            return $db->getObjListBySql($sql);
        }
    }

    function get_hot_articles() {
        global $db, $redis;
        $hots = $redis->get('HOT:ARTICLES');
        if ($hots) {
            return json_decode($hots);
        } else {
            $lastday = date("Y-m-d", strtotime("-7 day"));
            $sql = 'select * from article where `date` >= "'.$lastday.'" order by `index` desc limit 0, 10';
            $data = $db->getObjListBySql($sql);
            return $data;
        }
    }

    function get_hot_accounts() {
        global $db, $redis;
        $hot_accounts = $redis->get('HOT:ACCOUNTS');
        if ($hot_accounts) {
            return json_decode($hot_accounts);
        } else {
            $last7day = date("Y-m-d", strtotime("-3 day"));
            $sql = 'select aid, `name` as account_name, sum(`read`) as allread, sum(agree) as allagree from article left join account on account_id = aid where `date`>"'.$last7day.'" group by account_id order by allagree desc limit 0, 10';
            return $db->getObjListBySql($sql);
        }
    }


    function get_article($titleid) {
        global $db;
        $sql = 'select *, `desc` as article_desc from article where titleid="'.$titleid.'"';
        $sql = 'select *, account.desc as account_desc from ('.$sql.') t left join account on t.account_id = account.aid';
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
        $sql = 'select * from article where account_id = "'.$accountid.'" order by `date` desc, `index` desc limit 0, 10';
        return $db->getObjListBySql($sql);
    }

    function get_more_articles($articleid) {
        global $db;
        $sql = 'select * from article where articleid > "'.$articleid.'" limit 0, 10';
        return $db->getObjListBySql($sql);
    }


    function get_account($accountid, $page, $cpp=40) {
        global $db;
        $account = $db->getDataByAtr('account', 'aid', $accountid);
        if (count($account) == 0) {
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            include("404.php");
            exit();
        }
        $c = $db->getCountByAtr('article', 'account_id', $accountid);
        $hasmore = $c > $page*$cpp;
        $sql = 'select * from article where account_id = "'.$accountid.'" order by `date` desc, `index` desc';
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

    function get_videos($page, $cpp=5) {
        global $db;
        $sql = 'select count(*) as c from article where `video` != ""';
        $c = $db->getObjListBySql($sql);
        $c = $c[0]->c;
        $hasmore = $c > $page*$cpp;

        $sql = 'select * from article where `video` != "" order by `date` desc, `index` desc';
        $start = ($page-1) * $cpp;
        $sql .= ' limit '.$start.', '.$cpp;
        return array($db->getObjListBySql($sql), $hasmore);
    }

    function get_hot_videos() {
        global $db, $redis;
        $hot_videos = $redis->get('HOT:VIDEOS');
        if ($hot_videos) {
            return json_decode($hot_videos);
        } else {
            $lastday = date("Y-m-d", strtotime("-7 day"));
            $sql = 'select * from article where `date` > "'.$lastday.'" and `video` != "" order by `index` desc limit 0, 10';
            $data = $db->getObjListBySql($sql);
            return $data;
        }
    }

    function get_videos_with_account($page, $cpp=20) {
        global $db;
        $sql = 'select count(*) as c from article where `video` != ""';
        $c = $db->getObjListBySql($sql);
        $c = $c[0]->c;
        $hasmore = $c > $page*$cpp;

        $sql = 'select *, account.name as account_name, account.desc as account_desc, article.desc as article_desc from article left join account on account_id = aid where `video` != "" order by `date` desc, `index` desc';
        $start = ($page-1) * $cpp;
        $sql .= ' limit '.$start.', '.$cpp;
        return array($db->getObjListBySql($sql), $hasmore);
    }
?>
