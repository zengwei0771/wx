<div class="head">
    <div class="left">
        <a href="http://<?php echo $m_site_domain;?>/" title="<?php echo $site_name;?>">微信湾</a>
    </div>
    <div class="right">
        <ul id="menu">
            <li><a href="javascript:void(0);" onclick="$('#catagory').toggle('fast');">更多</a></li>
            <li><a href="/newest/" title="微信湾最新发布">最新</a></li>
            <li><a href="/hotagree/" title="微信湾点赞热门">点赞</a></li>
            <li><a href="/videos/" title="微信湾视频聚合">视频</a></li>
            <li><a href="/hotread/" title="微信湾热门排行">热门</a></li>
        </ul>
    </div>
</div>
<ul id="catagory">
    <?php
    foreach($catagorys as $c) {
        echo '<li><a href="/catagory/'.$c->catagoryid.'/" title="'.$c->catagory.'"';
        if (isset($_GET['catagory']) && $c->catagoryid == $_GET['catagory']) {
            echo ' class="active"';
        }
        echo '>'.$c->catagory.'</a></li>';
    }
    ?>
</ul>
