<?php
use tools\SendMail;
function timeDiff($AA_A__, $AA_A_A)
{
    if ($AA_A__ < $AA_A_A) {
	    $A_A_AA = $AA_A_A;
	    $A_AA__ = $AA_A__;
	    $A_A_AA = $AA_A__;
	    $A_AA__ = $AA_A_A;
	    $Y8sIaoz = 0;
	    $A_AA_A = $A_AA__ - $A_A_AA;
	    $A_AAA_ = intval($A_AA_A / 86400);
	    $A_AAAA = $A_AA_A % 86400;
	    $AA____ = intval($A_AAAA / 3600);
	    $A_AAAA = $A_AAAA % 3600;
	    $AA___A = intval($A_AAAA / 60);
	    $AA__A_ = $A_AAAA % 60;
	    $AA__AA = array('day' => $A_AAA_, 'hour' => $AA____, 'min' => $AA___A, 'sec' => $AA__A_);
	    return $AA__AA;
    }
  
}
function mystr($AAAA__)
{
    $AAA___ = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $AAA__A = array_rand($AAA___, $AAAA__);
    $AAA_A_ = '';
    for ($AAA_AA = 0; $AAA_AA < $AAAA__; $AAA_AA++) {
        $AAA_A_ .= $AAA___[$AAA__A[$AAA_AA]];
        $Y8snWE4 = $AAA_A_;
    }
    return $AAA_A_;
}
function sendcode($A_____A, $A____A_, $A____AA)
{

    $AAAAAA = new SendMail();
    $A______ = $AAAAAA->sendEmail($A_____A, $A____A_, $A____AA);
    if ($A______) {
        return 200;
    } else {
        return 404;
    }
}
function return_code($A___AAA, $A__A___)
{
    $A___AA_ = array('status' => $A___AAA, 'msg' => $A__A___);
    return json($A___AA_);
}
function posturl($A__AAA_, $A__AAAA)
{
    $A__AAAA = json_encode($A__AAAA);
    $A__A_AA = array("Content-type:application/json;charset='utf-8'", 'Accept:application/json');
    $A__AA__ = curl_init();
    curl_setopt($A__AA__, CURLOPT_URL, $A__AAA_);
    curl_setopt($A__AA__, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($A__AA__, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($A__AA__, CURLOPT_POST, 1);
    curl_setopt($A__AA__, CURLOPT_POSTFIELDS, $A__AAAA);
    curl_setopt($A__AA__, CURLOPT_HTTPHEADER, $A__A_AA);
    curl_setopt($A__AA__, CURLOPT_RETURNTRANSFER, 1);
    $A__AA_A = curl_exec($A__AA__);
    curl_close($A__AA__);
    return json_decode($A__AA_A, true);
}
function jsondata($A_A__A_)
{
    $A_A__A_['jifei'] = 'mizhi 4.2';
    return json($A_A__A_);
}
function jiem($A_AAA_A, $A_AAAA_ = 'bage')
{
    $A_AAA_A = urldecode($A_AAA_A);
    $A_A_A_A = '';
    $A_A_AA_ = $A_AAA_A[0];
    $A_A_AAA = strpos($A_A_A_A, $A_A_AA_);
    $A_AA___ = md5($A_AAAA_ . $A_A_AA_);
    $A_AA___ = substr($A_AA___, $A_A_AAA % 8, $A_A_AAA % 15);
    $A_AAA_A = substr($A_AAA_A, 1);
    $A_AA__A = '';
    $A_AA_A_ = 0;
    $A_AA_AA = 0;
    $A_AAA__ = 0;
    for ($A_AA_A_ = 0; $A_AA_A_ < strlen($A_AAA_A); $A_AA_A_++) {
        if ($A_AAA__ == strlen($A_AA___)) {
            $Y8sED = 0;
        } else {
            $Y8sED = $A_AAA__;
        }
        $A_AAA__ = $Y8sED;
        $A_AAA__++;
        $A_AA_AA = strpos($A_A_A_A, $A_AAA_A[$A_AA_A_]) - $A_A_AAA - ord($A_AA___[$A_AAA__]);
        if ($A_AA_AA < 0) {
            $A_AA_AA += 64;
            $Y8snWE6 = $A_AA_AA;
        }
        $A_AA__A .= $A_A_A_A[$A_AA_AA];
    }
    return base64_decode($A_AA__A);
}