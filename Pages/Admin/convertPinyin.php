<?php
    // session_start();
    include_once "../../SQL_Queries/connection.php";
    $user_id = $_SESSION["user_id"];
?>
<?php
function getPriority($letter) {
    if ($letter == "a") {
        return 5;
    } else if ($letter == "o") {
        return 4;
    } else if ($letter == "e") {
        return 3;
    } else if ($letter == "i") {
        return 2;   
    } else if ($letter == "u") {
        return 1;
    } else {
        return 0;
    }
}

function getHighestIndex($pinyin) {
    $prio = 0;
    $max = 0;
    for ($i = 0; $i < strlen($pinyin); $i++) {
        if (getPriority($pinyin[$i]) > $prio) {
            $max = $i;
            $prio = getPriority($pinyin[$i]);
        }
    }
    return $max;
}

function convertChar($letter, $tone) {
    if ($tone == 1) {
        switch ($letter) {
            case 'a': return 'ā';
            case 'o': return 'ō';
            case 'e': return 'ē';
            case 'i': return 'ī';
            case 'u': return 'ū';
        }
    } else if ($tone == 2) {
        switch ($letter) {
            case 'a': return 'á';
            case 'o': return 'ó';
            case 'e': return 'é';
            case 'i': return 'í';
            case 'u': return 'ú';
        }
    } else if ($tone == 3) {
        switch ($letter) {
            case 'a': return 'ǎ';
            case 'o': return 'ǒ';
            case 'e': return 'ě';
            case 'i': return 'ǐ';
            case 'u': return 'ǔ';
        }
    } else if ($tone == 4) {
        switch ($letter) {
            case 'a': return 'à';
            case 'o': return 'ò';
            case 'e': return 'è';
            case 'i': return 'ì';
            case 'u': return 'ù';
        }
    } else if ($tone == 5) {
        return $letter;
    }
    return $letter;
}

function convert($pinyin) {
    $word = explode(" ", $pinyin);
    for ($i = 0; $i < count($word); $i++) {
        $len = strlen($word[$i]);
        if ($len == 0) continue;

        $toneChar = $word[$i][$len - 1];
        if (!is_numeric($toneChar)) {
            $tone = 5;
        } else {
            $tone = (int)$toneChar;
            $word[$i] = substr($word[$i], 0, $len - 1);
        }

        if (strlen($word[$i]) == 0) continue;

        $index = getHighestIndex($word[$i]);
        if ($index < 0 || $index >= strlen($word[$i])) continue;

        $char = mb_substr($word[$i], $index, 1, "UTF-8");
        $converted = convertChar($char, $tone);
        $word[$i] = mb_substr($word[$i], 0, $index, "UTF-8") . $converted . mb_substr($word[$i], $index + 1, null, "UTF-8");
    }
    return implode(" ", $word);
}
?>
