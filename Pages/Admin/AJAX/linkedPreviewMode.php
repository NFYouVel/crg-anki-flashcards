<?php
    session_start();
    include "../../../SQL_Queries/connection.php";
    $mode = $_GET["mode"];
?>
<?php if($mode == "preview"){ ?>
    <table>
        <caption style = "background-color: white; color: black;">Preview</caption>
        <tr>
            <th>Card ID</th>
            <th>Card Simplified</th>
            <th>Pinyin</th>
            <th>Word Class</th>
            <th id = 'long'>English</th>
            <th>Sentence Code</th>
            <th>Priority</th>
            <th id = 'long'>Sentence Simplified</th>
        </tr>
        <?php
            foreach($_SESSION["allLinks"] as $key => $links) {
                $cardID = $links["cardID"];
                $sentenceCode = $links["sentenceCode"];
                $priority = $links["priority"];

                $cardInfo = mysqli_query($con, "SELECT chinese_sc, pinyin, word_class, meaning_eng FROM cards WHERE card_id = $cardID");
                $cardInfo = mysqli_fetch_array($cardInfo);

                $sentenceInfo = mysqli_query($con, "SELECT chinese_sc FROM example_sentence WHERE sentence_code = '$sentenceCode'");
                $sentenceInfo = mysqli_fetch_array($sentenceInfo);

                $cardSc     = $cardInfo ? $cardInfo['chinese_sc']    : 'Not Found';
                $pinyin     = $cardInfo ? $cardInfo['pinyin']        : 'Not Found';
                $wordClass  = $cardInfo ? $cardInfo['word_class']    : 'Not Found';
                $meaningEng = $cardInfo ? $cardInfo['meaning_eng']   : 'Not Found';
                $sentSc     = $sentenceInfo ? $sentenceInfo['chinese_sc'] : 'Not Found';

                if(isset($_SESSION["validLinks"][$key])) {
                    echo "<tr style = 'background-color: green;'>";
                }
                else if(isset($_SESSION["invalidLinks"][$key])) {
                    echo "<tr style = 'background-color: red;'>";
                }
                echo "
                    <td>$cardID</td>
                    <td>$cardSc</td>
                    <td>$pinyin</td>
                    <td>$wordClass</td>
                    <td id='long'>$meaningEng</td>
                    <td>$sentenceCode</td>
                    <td>$priority</td>
                    <td id='long'>$sentSc</td>
                </tr>";
            }
        ?>
    </table>
<?php } else if($mode == "valid"){ ?>
    <table>
        <caption style = "background-color: green;">Valid Links</caption>
        <tr>
            <th>Card ID</th>
            <th>Card Simplified</th>
            <th>Pinyin</th>
            <th>Word Class</th>
            <th id = 'long'>English</th>
            <th>Sentence Code</th>
            <th>Priority</th>
            <th id = 'long'>Sentence Simplified</th>
        </tr>
        <?php
            foreach($_SESSION["validLinks"] as $links) {
                $cardID = $links["cardID"];
                $sentenceCode = $links["sentenceCode"];
                $priority = $links["priority"];
                
                $cardInfo = mysqli_query($con, "SELECT chinese_sc, pinyin, word_class, meaning_eng FROM cards WHERE card_id = $cardID");
                $cardInfo = mysqli_fetch_array($cardInfo);

                $sentenceInfo = mysqli_query($con, "SELECT chinese_sc FROM example_sentence WHERE sentence_code = '$sentenceCode'");
                $sentenceInfo = mysqli_fetch_array($sentenceInfo);

                $cardSc     = $cardInfo ? $cardInfo['chinese_sc']    : 'Not Found';
                $pinyin     = $cardInfo ? $cardInfo['pinyin']        : 'Not Found';
                $wordClass  = $cardInfo ? $cardInfo['word_class']    : 'Not Found';
                $meaningEng = $cardInfo ? $cardInfo['meaning_eng']   : 'Not Found';
                $sentSc     = $sentenceInfo ? $sentenceInfo['chinese_sc'] : 'Not Found';

                echo "
                <tr>
                    <td>$cardID</td>
                    <td>$cardSc</td>
                    <td>$pinyin</td>
                    <td>$wordClass</td>
                    <td id='long'>$meaningEng</td>
                    <td>$sentenceCode</td>
                    <td>$priority</td>
                    <td id='long'>$sentSc</td>
                </tr>";
            }
        ?>
    </table>
<?php } else if($mode == "invalid"){ ?>
    <table>
        <caption style = "background-color: red;">Invalid Links</caption>
        <tr>
            <th>Card ID</th>
            <th>Card Simplified</th>
            <th>Pinyin</th>
            <th>Word Class</th>
            <th id = 'long'>English</th>
            <th>Sentence Code</th>
            <th>Priority</th>
            <th id = 'long'>Sentence Simplified</th>
            <th>Reason</th>
        </tr>
        <?php
            foreach($_SESSION["invalidLinks"] as $links) {
                $cardID = $links["cardID"];
                $sentenceCode = $links["sentenceCode"];
                $priority = $links["priority"];
                $reason = $links["reason"];
                
                $cardInfo = mysqli_query($con, "SELECT chinese_sc, pinyin, word_class, meaning_eng FROM cards WHERE card_id = $cardID");
                $cardInfo = mysqli_fetch_array($cardInfo);

                $sentenceInfo = mysqli_query($con, "SELECT chinese_sc FROM example_sentence WHERE sentence_code = '$sentenceCode'");
                $sentenceInfo = mysqli_fetch_array($sentenceInfo);

                $cardSc     = $cardInfo ? $cardInfo['chinese_sc']    : 'Not Found';
                $pinyin     = $cardInfo ? $cardInfo['pinyin']        : 'Not Found';
                $wordClass  = $cardInfo ? $cardInfo['word_class']    : 'Not Found';
                $meaningEng = $cardInfo ? $cardInfo['meaning_eng']   : 'Not Found';
                $sentSc     = $sentenceInfo ? $sentenceInfo['chinese_sc'] : 'Not Found';

                echo "
                <tr>
                    <td>$cardID</td>
                    <td>$cardSc</td>
                    <td>$pinyin</td>
                    <td>$wordClass</td>
                    <td id='long'>$meaningEng</td>
                    <td>$sentenceCode</td>
                    <td>$priority</td>
                    <td id='long'>$sentSc</td>
                    <td>$reason</td>
                </tr>";
            }
        ?>
    </table>
<?php } ?>  
