<?php // API for coins.ini configuration file.

function getDescription() {
    if(apcu_exists("DESCRIPTION_SC"))
        return apcu_fetch("DESCRIPTION_SC");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 12) === "DESCRIPTION:") {
            fclose($coin_ini);
            apcu_store("DESCRIPTION_SC", trim(substr($line, 12)));
            return trim(substr($line, 12));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"DESCRIPTION:\" is absent from the coins.ini file!");
}

function getKeywords() {
    if(apcu_exists("KEYWORDS_SC"))
        return apcu_fetch("KEYWORDS_SC");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 9) === "KEYWORDS:") {
            fclose($coin_ini);

            $keywords = explode(",", trim(substr($line, 9)));
            for($i = 0; $i < count($keywords); $i++) {
                $keywords[$i] = trim($keywords[$i]);
            }

            apcu_store("KEYWORDS_SC", $keywords);
            return $keywords;
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"KEYWORDS:\" is absent from the coins.ini file!");
}

function getExplorer() {
    if(apcu_exists("BTC_EXPLORER_SC"))
    return apcu_fetch("BTC_EXPLORER_SC");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 13) === "BTC_EXPLORER:") {
            fclose($coin_ini);
            apcu_store("BTC_EXPLORER_SC", trim(substr($line, 13)));
            return trim(substr($line, 13));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"BTC_EXPLORER:\" is absent from the coins.ini file!");
}

function getExplorerAPI() {
    if(apcu_exists("EXPLORER_API_SC"))
        return apcu_fetch("EXPLORER_API_SC");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 13) === "EXPLORER_API:") {
            fclose($coin_ini);
            apcu_store("EXPLORER_API_SC", trim(substr($line, 13)));
            return trim(substr($line, 13));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"EXPLORER_API:\" is absent from the coins.ini file!");
}

function getPriceAPI() {
    if(apcu_exists("PRICE_API_SC"))
        return apcu_fetch("PRICE_API_SC");

    $coin_ini = fopen("coins.ini", "r") or exit("Error: File \"coins.ini\" does not exist on the server!");
    while(!feof($coin_ini)) {
        $line = trim(fgets($coin_ini));
        if(substr($line, 0, 10) === "PRICE_API:") {
            fclose($coin_ini);
            apcu_store("PRICE_API_SC", trim(substr($line, 10)));
            return trim(substr($line, 10));
        }
    }

    fclose($coin_ini);
    exit("Error! The line beginning with \"PRICE_API:\" is absent from the coins.ini file!");
}

function getChains() {
    if(apcu_exists("CHAIN_DATA_SC"))
        return apcu_fetch("CHAIN_DATA_SC");

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

    apcu_store("CHAIN_DATA_SC", $chains);
    return $chains;
}

// Clear all coins.ini cache data
function clearCache() {
    apcu_delete("DESCRIPTION_SC");
    apcu_delete("KEYWORDS_SC");
    apcu_delete("BTC_EXPLORER_SC");
    apcu_delete("EXPLORER_API_SC");
    apcu_delete("PRICE_API_SC");
    apcu_delete("CHAIN_DATA_SC");
}