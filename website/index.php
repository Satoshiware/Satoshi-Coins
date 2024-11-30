<?php
$_SERVER['SERVER_NAME'] = "ko"; // todo: why is it not finding this? Where is it not finding it.????????????????????????????????????????????????

/*
if(function_exists('apcu_enabled') && apcu_enabled()) {
    echo "APCU:          It is Available";
  } else {
        echo "APCU:          Not Available; Function Exists: ";
        if(function_exists('apcu_enabled')) {
            echo "yes; APCU Enabled: ";
            if(apcu_enabled()) {
                echo "yes";
            } else {
                echo "no";
            }
        } else {
            echo "no";
        }
    }
*/


// Form the string of keywords for this page
$key_array = getKeywords();
$keywords = "";
foreach($key_array as $word)
    $keywords .= $word . ", ";
$keywords = substr($keywords, 0, -2); // Remove the last two characters (comma and space)

// Process the input text in the Satoshi Coins' search bar
if(!empty($_GET["search"])) {
    $_GET["search"] = trim($_GET["search"]); // Trim leading and trailing spaces; convert all to lower case.

    // Check for a Bech32 truncated address
    if (preg_match("/^[ac-hj-np-z02-9]{9}$/", strtolower($_GET["search"]))) {
        global $db;
        connect();

        $coin_stmt = $db->prepare("SELECT * FROM coins WHERE truncation = ?");
        $coin_stmt->execute([$_GET["search"]]);
        $coins = $coin_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Satoshi Coins</title>

        <link href="style.css" rel="stylesheet">
        <meta charset="utf-8" />

        <meta name="viewport" content="width=device-width" />
        <meta name="description" content="<?php echo getDescription();?>" />
        <meta name="keywords" content="<?php echo $keywords;?>" />
    </head>
    <body>
        <br><br>
        <div class="search-box">
            <form action="index.php" method="get">
                <label for="search"></label><input type="search" id="search" name="search" placeholder="Satoshi Coins" />
                <input type="image" id="search_icon" src="search_icon.png" alt="search" />
            </form>
        </div>

        <?php if(!empty($coins)) { ?>
            <div class="framePadding"><div class="frame">
                <?php foreach($coins as $coin) { ?>
                    <div class="number"><br />
                        Coin Number: <?php echo $coin['coin_id']; ?>
                    </div><br /><br />
                    <div class="address">
                        Address: <a href="<?php echo getExplorer(); ?>/address/<?php echo $coin['address']; ?>"><?php echo substr($coin['address'], 0, 3); ?><div class="truncation"><?php echo $coin['truncation']; ?></div><?php echo substr($coin['address'], 12, 4) . "..."; ?></a>
                    </div><br /><br />
                    <div class="value">
                        Value: <?php echo number_format($coin['satoshis']); ?> $ATS
                    </div><br /><br />
                    <div class="redeemed">
                        Redeemed: <?php echo $coin['spent'] != 0 ? "YES" : "NO"; ?>
                    </div><br /><br />
                <?php } ?>
            </div></div>
        <?php } ?>

        <div class="framePadding"><div class="frame">
            A bitcoin can be divided into 100 Million units.<br />
            These units are called Satoshis ($ATS).<br />

            <table class="table">
                <tr><td>฿1.00&nbsp;=&nbsp;100,000,000&nbsp;$ATS</td></tr>
                <tr><td>1&nbsp;$AT&nbsp;=&nbsp;฿0.00000001</td></tr>
                <tr><td><label id="coinAmount">1,000,000&nbsp;$ATS</label>&nbsp=&nbsp<label id="usd">$1,000.00</label></td></tr>
                <tr><td>USD&nbsp;=&nbsp;<label id="satoshis">0&nbsp;$ATS</label></td></tr>
            </table>

            <h3>Collect All Seven Coins</h3>
            <table class="table">
                <tr><td>•<label class="satoshiClick">MONEY&nbsp;WITHOUT&nbsp;BORDERS</label></td></tr>
                <tr><td class="cellSatoshis">1,000,000 $ATS</td></tr>

                <tr><td>•<label class="satoshiClick">DIGITAL&nbsp;GOLD</label></td></tr>
                <tr><td class="cellSatoshis">500,000 $ATS</td></tr>

                <tr><td>•<label class="satoshiClick">DECENTRALIZED&nbsp;MONETARY&nbsp;POWER</label></td></tr>
                <tr><td class="cellSatoshis">250,000 $ATS</td></tr>

                <tr><td>•<label class="satoshiClick">TIME&nbsp;FOR&nbsp;PLAN&nbsp;₿</label></td></tr>
                <tr><td class="cellSatoshis">100,000 $ATS</td></tr>

                <tr><td>•<label class="satoshiClick">TECHNOLOGICAL&nbsp;TOUR&nbsp;DE&nbsp;FORCE</label></td></tr>
                <tr><td class="cellSatoshis">50,000 $ATS</td></tr>

                <tr><td>•<label class="satoshiClick">RULES&nbsp;WITHOUT&nbsp;RULERS</label></td></tr>
                <tr><td class="cellSatoshis">25,000 $ATS</td></tr>

                <tr><td>•<label class="satoshiClick">ONE&nbsp;COIN&nbsp;TO&nbsp;RULE&nbsp;‘EM&nbsp;ALL</label></td></tr>
                <tr><td class="cellSatoshis">10,000 $ATS</td></tr>
            </table>

            <br /><div class="timeStamp" id="timeStamp">Last Updated: ?</div><br /><br />
        </div></div>

        <script src="jquery-3.6.0.min.js"></script>
        <script type="text/javascript">
            let coin = 1000000;
            const api = "<?php echo getPriceAPI();?>";

            $(document).ready(function () {
                let coinP = (new URLSearchParams(window.location.search)).get('coin');
                if(coinP === "1000000" || coinP === "500000" || coinP === "250000" || coinP === "100000" || coinP === "50000" || coinP === "25000" || coinP === "10000")
                    coin = coinP;

                updatePrice();
                setInterval(function () { updatePrice(); }, 100000); // updatePrice() every one hundred seconds
            });

            $('.satoshiClick').click(function () {
                if($(this).html() === "MONEY&nbsp;WITHOUT&nbsp;BORDERS") {
                    coin = 1000000;
                    $('#coinAmount').html("1,000,000 $ATS");
                }else if($(this).html() === "DIGITAL&nbsp;GOLD") {
                    coin = 500000;
                    $('#coinAmount').html("500,000 $ATS");
                }else if($(this).html() === "DECENTRALIZED&nbsp;MONETARY&nbsp;POWER") {
                    coin = 250000;
                    $('#coinAmount').html("250,000 $ATS");
                }else if($(this).html() === "TIME&nbsp;FOR&nbsp;PLAN&nbsp;₿") {
                    coin = 100000;
                    $('#coinAmount').html("100,000 $ATS");
                }else if($(this).html() === "TECHNOLOGICAL&nbsp;TOUR&nbsp;DE&nbsp;FORCE") {
                    coin = 50000;
                    $('#coinAmount').html("50,000 $ATS");
                }else if($(this).html() === "RULES&nbsp;WITHOUT&nbsp;RULERS") {
                    coin = 25000;
                    $('#coinAmount').html("25,000 $ATS");
                }else if($(this).html() === "ONE&nbsp;COIN&nbsp;TO&nbsp;RULE&nbsp;‘EM&nbsp;ALL") {
                    coin = 10000;
                    $('#coinAmount').html("10,000 $ATS");
                }
                    updatePrice();
            });

            function updatePrice() {
                $.getJSON(api, function (data) {
                    $('#series').html(coin.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                    $('#usd').html("$" + parseFloat(data.bpi.USD.rate_float * coin / 100000000).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    $('#satoshis').html(parseFloat(1 / data.bpi.USD.rate_float * 100000000).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "&nbsp;$ATS");

                    let date = new Date(data.time.updatedISO);
                    let hour = 0;
                    if (date.getHours() > 0 && date.getHours() <= 12) hour = date.getHours();
                        else if (date.getHours() > 12) hour = date.getHours() - 12;
                        else if (date.getHours() === 0) hour = 12;
                        $('#timeStamp').html("Last Updated: " + hour + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + (date.getHours() >= 12 ? " P.M." : " A.M.") + " (" + (date.getMonth() + 1) + '/' + date.getDate() + "/" + date.getFullYear() + ")");
                });
            }
        </script>
    </body>

    <footer>

    </footer>
</html>
