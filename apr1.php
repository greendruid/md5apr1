<?php
// 
function apr1_salt() {
        $saltern = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $saltern .= './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $salt = '';
        for($i=0; $i<8; $i++)
                $salt .= $saltern[rand(0, 127)];
        return $salt;
}
//
function apr1($mdp, $salt) {
        $max = strlen($mdp);
        $context = $mdp.'$apr1$'.$salt;
        $binary = pack('H32', md5($mdp.$salt.$mdp));
        for($i=$max; $i>0; $i-=16)
                $context .= substr($binary, 0, min(16, $i));
        for($i=$max; $i>0; $i>>=1)
                $context .= ($i & 1) ? chr(0) : $mdp{0};
        $binary = pack('H32', md5($context));
        for($i=0; $i<1000; $i++) {
                $new = ($i & 1) ? $mdp : $binary;
                if($i % 3) $new .= $salt;
                if($i % 7) $new .= $mdp;
                $new .= ($i & 1) ? $binary : $mdp;
                $binary = pack('H32', md5($new));
        }
        $hash = '';
        for ($i = 0; $i < 5; $i++) {
                $k = $i+6;
                $j = $i+12;
                if($j == 16) $j = 5;
                $hash = $binary{$i}.$binary{$k}.$binary{$j}.$hash;
        }
        $hash = chr(0).chr(0).$binary{11}.$hash;
        $hash = strtr(
                strrev(substr(base64_encode($hash), 2)),
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
                './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'
        );
        return '$apr1$'.$salt.'$'.$hash;
}
// it's dirty dirty :)))
$res = apr1("argv[1]", apr1_salt());
//
echo $res."\n";
?>
