<?php

header("Content-type:image/png");
$newImg=ImageCreate(32,32);
$skyblue=ImageColorAllocate($newImg,136,193,255);
ImageFill($newImg,0,0,$skyblue);
ImagePNG($newImg);
ImageDestroy($newImg);

?>
