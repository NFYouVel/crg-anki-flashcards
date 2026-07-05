<?php
function getPriority($letter)
{
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
    } else if ($letter == "ü" || $letter == "v") {
        // v sering dipakai sebagai pengganti ü di input
        return 1;
    } else {
        return 0;
    }
}

function getHighestIndex($pinyin)
{
    $prio = 0;
    $max = 0;
    $len = mb_strlen($pinyin, "UTF-8");  // ← FIX: pakai mb_strlen
    for ($i = 0; $i < $len; $i++) {
        $char = mb_substr($pinyin, $i, 1, "UTF-8");  // ← FIX: pakai mb_substr
        if (getPriority($char) > $prio) {
            $max = $i;
            $prio = getPriority($char);
        }
    }
    return $max;
}

function convertChar($letter, $tone)
{
    if ($tone == 1) {
        switch ($letter) {
            case 'a':
                return 'ā';
            case 'o':
                return 'ō';
            case 'e':
                return 'ē';
            case 'i':
                return 'ī';
            case 'u':
                return 'ū';
            case 'ü':
                return 'ǖ';
            case 'v':
                return 'ǖ';
        }
    } else if ($tone == 2) {
        switch ($letter) {
            case 'a':
                return 'á';
            case 'o':
                return 'ó';
            case 'e':
                return 'é';
            case 'i':
                return 'í';
            case 'u':
                return 'ú';
            case 'ü':
                return 'ǘ';
            case 'v':
                return 'ǘ';
        }
    } else if ($tone == 3) {
        switch ($letter) {
            case 'a':
                return 'ǎ';
            case 'o':
                return 'ǒ';
            case 'e':
                return 'ě';
            case 'i':
                return 'ǐ';
            case 'u':
                return 'ǔ';
            case 'ü':
                return 'ǚ';
            case 'v':
                return 'ǚ';
        }
    } else if ($tone == 4) {
        switch ($letter) {
            case 'a':
                return 'à';
            case 'o':
                return 'ò';
            case 'e':
                return 'è';
            case 'i':
                return 'ì';
            case 'u':
                return 'ù';
            case 'ü':
                return 'ǜ';
            case 'v':
                return 'ǜ';
        }
    } else if ($tone == 5) {
        // Tone 5 = neutral tone, tidak ada tone mark
        // ü tetap ü, v diubah jadi ü
        if ($letter == 'v')
            return 'ü';
        return $letter;
    }
    return $letter;
}

function convert($pinyin)
{
    $word = explode(" ", $pinyin);
    for ($i = 0; $i < count($word); $i++) {
        $len = mb_strlen($word[$i], "UTF-8");  // ← FIX: pakai mb_strlen
        if ($len == 0)
            continue;

        // Ambil karakter terakhir (bukan byte terakhir)
        $toneChar = mb_substr($word[$i], $len - 1, 1, "UTF-8");  // ← FIX
        if (!is_numeric($toneChar)) {
            $tone = 5;
        } else {
            $tone = (int) $toneChar;
            // Hapus tone number pakai mb_substr
            $word[$i] = mb_substr($word[$i], 0, $len - 1, "UTF-8");  // ← FIX
        }

        if (mb_strlen($word[$i], "UTF-8") == 0)
            continue;  // ← FIX

        $index = getHighestIndex($word[$i]);
        if ($index < 0 || $index >= mb_strlen($word[$i], "UTF-8"))
            continue;  // ← FIX

        $char = mb_substr($word[$i], $index, 1, "UTF-8");
        $converted = convertChar($char, $tone);
        $word[$i] = mb_substr($word[$i], 0, $index, "UTF-8") . $converted . mb_substr($word[$i], $index + 1, null, "UTF-8");
    }
    return implode(" ", $word);
}
?>