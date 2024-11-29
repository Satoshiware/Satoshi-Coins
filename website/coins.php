<?php // API for coins.ini configuration file.

// If upload is enabled for a particular user, the following script is executed:
/*if(apcu_exists("UPLOAD" . $_SERVER['SERVER_NAME'] . $_SERVER['REMOTE_ADDR'])) {
    // If there is no "post" upload data then exit.
    if(empty($_FILES)) {
        header("Location: " . getURL());
        exit();
    }

    // Delete (clear) the upload flag.
    apcu_delete("UPLOAD" . $_SERVER['SERVER_NAME'] . $_SERVER['REMOTE_ADDR']);

    // Verify uploaded file meets specifications: NAME = "coins.ini" && Size <= 25 kB
    if(strtolower($_FILES["fileToUpload"]["name"]) != "coins.ini")
        exit("Sorry, wrong file name.");
    if($_FILES["fileToUpload"]["size"] > 25000)
        exit("Sorry, file is too large.");

    // Write the uploaded coins.ini file to the Satoshi Coins' directory.
    copy("coins.ini", "coins.ini.bak");
    if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "./" . basename($_FILES["fileToUpload"]["name"])))
        exit("Sorry, there was an error uploading the file.");
    exit("The file has been successfully uploaded.");
}else if(realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    // If this file (coins.php) is accessed directly without upload enabled, redirect to the main (Satoshi Coins) page.
    header("Location: " . getURL());
}*/

function getURL() {
    if(apcu_exists("URL_j7w13wd"))
        return apcu_fetch("URL_j7w13wd");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 4) === "URL:") {
            fclose($coin_ini);
            apcu_store("URL_j7w13wd", trim(substr($line, 4)));
            return trim(substr($line, 4));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"URL:\" is absent from the coins.ini file!");
}

function getDescription() {
    if(apcu_exists("DESCRIPTION_j7w13wd"))
        return apcu_fetch("DESCRIPTION_j7w13wd");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 12) === "DESCRIPTION:") {
            fclose($coin_ini);
            apcu_store("DESCRIPTION_j7w13wd", trim(substr($line, 12)));
            return trim(substr($line, 12));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"DESCRIPTION:\" is absent from the coins.ini file!");
}

function getKeywords() {
    if(apcu_exists("KEYWORDS_j7w13wd"))
        return apcu_fetch("KEYWORDS_j7w13wd");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 9) === "KEYWORDS:") {
            fclose($coin_ini);

            $keywords = explode(",", trim(substr($line, 9)));
            for($i = 0; $i < count($keywords); $i++) {
                $keywords[$i] = trim($keywords[$i]);
            }

            apcu_store("KEYWORDS_j7w13wd", $keywords);
            return $keywords;
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"KEYWORDS:\" is absent from the coins.ini file!");
}

/*function getPasswdHash() {
    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 9) === "PASSWORD:") {
            fclose($coin_ini);
            return trim(substr($line, 9));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"PASSWORD:\" is absent from the coins.ini file!");
}*/

/*function writePasswdHash($hash = "") {
    if(copy("coins.ini", "coins.ini.bak")) { // Make a backup. If successful then continue.
        $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");

        $contents = "";
        while(!feof($coin_ini)) {
            $line = fgets($coin_ini);
            $trim = trim($line);
            if (substr($trim, 0, 9) === "PASSWORD:") {
                $contents = $contents . "PASSWORD: " . $hash . "\n";
            } else {
                $contents = $contents . $line;
            }
        }
        fclose($coin_ini);

        $coin_ini = fopen("coins.ini", "w") or exit("Error: Could not create or write to the \"coins.ini\" file!");
        fwrite($coin_ini, $contents);
        fclose($coin_ini);
    }else {
        exit("Error! Making a coin.ini file backup (coin.ini.bak) has failed.");
    }

    if(getPasswdHash() !== $hash) {
        copy("coins.ini.bak", "coins.ini");
        exit("Error! The password hash was not successfully written. The coin.ini backup (coins.ini.bak) has been restored.");
    }
    unlink("coins.ini.bak"); // Deletes file "coins.ini.bak".
}*/

// todo: update cache and clear and coins.ini file. Update notes above. it is modeled after Blockstream's way of listing it.
function getExplorer() {
    return "https://www.blockstream.info";
}

function getExplorerAPI() {
    if(apcu_exists("EXPLORER_API_j7w13wd"))
        return apcu_fetch("EXPLORER_API_j7w13wd");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 13) === "EXPLORER_API:") {
            fclose($coin_ini);
            apcu_store("EXPLORER_API_j7w13wd", trim(substr($line, 13)));
            return trim(substr($line, 13));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"EXPLORER_API:\" is absent from the coins.ini file!");
}

function getPriceAPI() {
    if(apcu_exists("PRICE_API_j7w13wd"))
        return apcu_fetch("PRICE_API_j7w13wd");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 10) === "PRICE_API:") {
            fclose($coin_ini);
            apcu_store("PRICE_API_j7w13wd", trim(substr($line, 10)));
            return trim(substr($line, 10));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"PRICE_API:\" is absent from the coins.ini file!");
}

function getChains() {
    if(apcu_exists("CHAIN_DATA_j7w13wd"))
        return apcu_fetch("CHAIN_DATA_j7w13wd");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    $chains = array('txid' => array(), 'frozen' => array());
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 6) === "CHAIN:" && preg_match("/^[a-f0-9]{64}$/", trim(strtolower(substr($line, 6))))) {
            array_push($chains['txid'], trim(strtolower(substr($line, 6))));
            $line = trim(fgets($coin_ini));
            if(substr($line, 0, 7) === "FROZEN:") {
                if(is_numeric(trim(substr($line, 7))))
                    array_push($chains['frozen'], trim(substr($line, 7)));
                else
                    array_push($chains['frozen'], 0);
            }else {
                fclose($coin_ini);
                exit("Error: Could not successfully parse the chain data from the \"coins.ini\" file!\n<br>" . $line);
            }
        }
    }
    fclose($coin_ini);

    if(empty($chains)) {
        exit("Error: Could not parse any chain data from the \"coins.ini\" file!\n<br>");
    }

    apcu_store("CHAIN_DATA_j7w13wd", $chains);
    return $chains;
}

/*function downloadCoinINI() {
    // Copy contents of coins.ini to coins.ini.tmp with the password hash removed. This new .tmp file is downloaded.
    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    $contents = "";
    while(!feof($coin_ini)) {
        $line = fgets($coin_ini);
        $trim = trim($line);
        if (substr($trim, 0, 9) === "PASSWORD:") {
            $contents = $contents . "PASSWORD:" . "\n";
        } else {
            $contents = $contents . $line;
        }
    }
    fclose($coin_ini);

    $coin_ini_tmp = fopen("coins.ini.tmp", "w") or exit("Error: Could not create or write to \"coins.ini.tmp\" file!");
    fwrite($coin_ini_tmp, $contents);
    fclose($coin_ini_tmp);

    // Prepare for download.
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename("coins.ini") . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize("coins.ini.tmp"));
    flush();
    readfile("coins.ini.tmp") or exit("Error: File \"coins.ini.tmp\" does not exist on the server!");
    exit();
}*/

/*function uploadCoinINI() {
    // Generate form to upload the coins.ini file
    echo '<form action="coins.php" method="post" enctype="multipart/form-data">';
    echo '    <br><br><br><input type="file" name="fileToUpload" id="fileToUpload">';
    echo '    <br><br><br><input type="submit" value="Upload File" name="submit">';
    echo '</form>';

    // Unlock uploads flag for this user. Time To Live: 60 seconds.
    apcu_store("UPLOAD" . $_SERVER['SERVER_NAME'] . $_SERVER['REMOTE_ADDR'], null,60);
}*/

// Clear all coins.ini cache data
function clearCache() {
    apcu_delete("URL_j7w13wd");
    apcu_delete("DESCRIPTION_j7w13wd");
    apcu_delete("KEYWORDS_j7w13wd");
    apcu_delete("EXPLORER_API_j7w13wd");
    apcu_delete("PRICE_API_j7w13wd");
    apcu_delete("CHAIN_DATA_j7w13wd");
}
