<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/7
 * Time: 17:00
 */
session_start();
$logincode = time();
$_SESSION['wx_logincode'] = $logincode;
$picpath = 'http://weixin.luqinwenda.com/get_login_qrcode.aspx?logincode=' . $logincode;
header('Content-type: image/jpeg');
$file = file_get_contents($picpath);
echo $file;