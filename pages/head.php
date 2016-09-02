<?php
    require_once('config.php');
    require_once('function.php');
    $catagorys = get_catagorys();
?>
<div class="head">
    <div class="left">
        <a href="http://<?php echo $site_domain;?>/" title="<?php echo $site_name;?>">
            <img src="/static/longlogo.png" height="90" width="180" alt="<?php echo $site_name;?>"></img>
        </a>
    </div>
    <div class="right">
        <ul class="navi">
        <?php
        foreach(array_reverse(array_slice($catagorys, 0, 10)) as $c) {
            echo '<li><a href="/catagory/'.$c->catagoryid.'/" title="'.$c->catagory.'">'.$c->catagory.'</a><img src="/static/navi-split.png"/></li>';
        }
        foreach(array_slice($catagorys, 10) as $c) {
            echo '<li class="hide"><a href="/catagory/'.$c->catagoryid.'/" title="'.$c->catagory.'">'.$c->catagory.'</a><img src="/static/navi-split.png"/></li>';
        }
        ?>
        </ul>
    </div>
</div>
