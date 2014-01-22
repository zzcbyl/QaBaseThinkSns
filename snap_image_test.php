<?php
/**
 * Created by PhpStorm.
 * User: cangjie
 * Date: 1/22/14
 * Time: 15:37
 */

$img_binary_data = @file_get_contents("http://www.nanshanski.com/web_cn/images/gg.jpg");
$img_size = @file_put_contents("/Library/WebServer/Documents/QaBaseThinkSns/test.jpg",$img_binary_data);
echo $img_size;