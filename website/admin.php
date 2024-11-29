<?php
require "coins.php"; // API for coins.ini configuration file.

define("DB_NAME", "db.sqlite3"); // Name of database file

// Satoshi Coins' database tables
define("CHAINS_TABLE", "CREATE TABLE chains(
		chain_id INTEGER PRIMARY KEY,
		op_return TEXT NOT NULL,
        frozen NUMERIC NOT NULL);");
define("TXIDS_TABLE", "CREATE TABLE txids(
        txid_id INTEGER PRIMARY KEY,
		chain_id INTEGER NOT NULL,
		txid TEXT NOT NULL,
		FOREIGN KEY(chain_id) REFERENCES chains(chain_id));");
define("COINS_TABLE", "CREATE TABLE coins(
        coin_id INTEGER PRIMARY KEY,
		txid_id INTEGER NOT NULL,
		address TEXT NOT NULL,
		truncation TEXT NOT NULL,
		out INTEGER NOT NULL,
		satoshis INTEGER NOT NULL,
		spent NUMERIC NOT NULL,
		FOREIGN KEY(txid_id) REFERENCES txids(txid_id));");

// Used to create the prepared statements ($insert_chain, $insert_txid, $insert_coin) for the loadChain and loadData functions.
define("CHAIN_INSERT", "INSERT INTO chains (op_return, frozen) VALUES (:op_return, :frozen)");
define("TXID_INSERT", "INSERT INTO txids (chain_id, txid) VALUES (:chain_id, :txid)");
define("COIN_INSERT", "INSERT INTO coins(txid_id, address, truncation, out, satoshis, spent) VALUES (:txid_id, :address, :truncation, :out, :satoshis, :spent)");

define("BRUTE_SINGLE_TRIES", 3); // Number of password attempts before lockout for a single user.
define("BRUTE_BOTNET_TRIES", 10000); // Number of password attempts by everyone before complete lockout.
define("BRUTE_SINGLE_TIME", 600); // Wait time (seconds) after multiple password attempts for a single user.
define("BRUTE_BOTNET_TIME", 14400); // Wait time (seconds) after multiple password attempts by everyone.

/* globals
 *      $db             // PDO database for Satoshi Coins.
 *      $insert_chain   // Prepared statement to insert a new chain into the database.
 *      $insert_txid    // Prepared statement to insert a new txid into the database.
 *      $insert_coin    // Prepared statement to insert a new coin into the database.
*/

// Process commands from the administrator (aka Satoshi :-)
function adminCMD($cmd) {
    $parameters = explode(" ", $cmd); // Split the parameter(s) into an array.

    // Code to prevent brute force attacks from single users and botnets.
//    $brute_key_single = "{$_SERVER['SERVER_NAME']}~login:{$_SERVER['REMOTE_ADDR']}"; // Memory cache key to track number of login attempts for a single user.
//    if(!apcu_exists($brute_key_single))
//        apcu_store($brute_key_single, 0, BRUTE_SINGLE_TIME);
//    if(!apcu_exists("APCU_BOTNET_KEY_j7w13wd\""))
//        apcu_store("APCU_BOTNET_KEY_j7w13wd\"", 0, BRUTE_BOTNET_TIME);
//    apcu_inc($brute_key_single); // Increment number of password attempts for a single user.
//    apcu_inc("APCU_BOTNET_KEY_j7w13wd\""); // Increment number of password attempts by everyone.

    // Are there sufficient parameters to continue? Is the password being attacked?
//    if(count($parameters) < 2 || apcu_fetch($brute_key_single) > BRUTE_SINGLE_TRIES || apcu_fetch("APCU_BOTNET_KEY_j7w13wd") > BRUTE_BOTNET_TRIES) {
//        header("Location: " . getURL());
//        exit();
//    }

    // The "passwd" command is the only one that has two parameters. Do this here and do it first.
//    $hash = getPasswdHash();
//    if(strtolower($parameters[0]) == "passwd" && count($parameters) <= 3) {
//       if($hash == "" || (count($parameters) == 3 && password_verify($parameters[2], $hash))) {
//            writePasswdHash(password_hash($parameters[1], PASSWORD_DEFAULT));
//            apcu_delete($brute_key_single); // Reset the number of password attempts for a single user.
//            echo "The password has been updated";
//       }else {
//           header("Location: " . getURL());
//       }
//        exit();
//    }else if(count($parameters) > 2) { // Are there too many parameters?
//        header("Location: " . getURL());
//        exit();
//    }

    // Verify password
 //   if($hash == "" || !password_verify($parameters[1], $hash)) {
 //       header("Location: " . getURL());
//        exit();
//    }
//    apcu_delete($brute_key_single); // Reset the number of password attempts for a single user.

    if(strtolower($parameters[0]) == "build") {
        build();
    }else if(strtolower($parameters[0]) == "update") {
        update();
//    }else if(strtolower($parameters[0]) == "upload") {
//        clearCache(); // Clear all coins.ini cache data
//        uploadCoinINI();
//    }else if(strtolower($parameters[0]) == "download") {
//        downloadCoinINI();
    }
}

// Establish connection to the database
function connect() {
    global $db;

    try {
        $db = new PDO("sqlite:" . DB_NAME); // Create (or connect to) Satoshi Coins" SQLite database
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exceptions
    } catch (PDOException $error) {
        echo "Sorry: Connection to the Satoshi Coins' database could not be established.";
        exit();
    }
}

// Builds or rebuilds the database from scratch.
function build() {
    global $db, $insert_chain, $insert_txid, $insert_coin;
    clearCache(); // Clear all coins.ini cache data

    if(file_exists(DB_NAME)) {
        unlink(DB_NAME) or exit("Could not delete the database");
        sleep(3); // Wait 3 seconds before continuing.
    }

    try {
        connect(); // Connect to the Satoshi Coins' database.

        // Create tables
        $db->exec(CHAINS_TABLE);
        $db->exec(TXIDS_TABLE);
        $db->exec(COINS_TABLE);

        // Create prepared statements. These statements are global and are required for the loadChain and loadData functions.
        $insert_chain = $db->prepare(CHAIN_INSERT);
        $insert_txid = $db->prepare(TXID_INSERT);
        $insert_coin = $db->prepare(COIN_INSERT);

        // Loop through each chain collecting data on each coin.
        $chains = getChains();
        for($i = 0; $i < count($chains['txid']); $i++) {
            loadChain($chains['txid'][$i], (int)$chains['frozen'][$i]);
        }
    }catch (PDOException $error) {
        echo "Error: " . $error->getMessage() . "<br>";
        echo "Line: " . $error->getLine() . "<br>";
        exit();
    }

    exit("Database build is complete!");
}

// Updates the Satoshi Coins' database with the latest transactions and spent coins.
function update() {
    global $db, $insert_chain, $insert_txid, $insert_coin;
    clearCache(); // Clear all coins.ini cache data

    try {
        connect(); // Connect to the Satoshi Coins' database.

        // Create prepared statements. These statements are global and are required for the loadChain and loadData functions.
        $insert_chain = $db->prepare(CHAIN_INSERT);
        $insert_txid = $db->prepare(TXID_INSERT);
        $insert_coin = $db->prepare(COIN_INSERT);

        // Find all newly spent coins and update the database.
        $spent = array(); // Array for all newly spent coins.
        $txids_stmt = $db->query('SELECT txid_id, * FROM txids');
        while ($txids_row = $txids_stmt->fetch(PDO::FETCH_ASSOC)) {
            $txid_outspends = json_decode(file_get_contents(getExplorerAPI() . '/tx/' . $txids_row['txid'] . '/outspends'), true);
            $coins_stmt = $db->prepare('SELECT coin_id, out, spent FROM coins WHERE txid_id = ?');
            $coins_stmt->bindValue(1, $txids_row['txid_id'], PDO::PARAM_INT);
            $coins_stmt->execute();
            while ($coins_row = $coins_stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($txid_outspends[(int)$coins_row['out']]['spent'] xor $coins_row['spent']) { // Using XOR operator to classify newly spent coins.
                    array_push($spent, (int)$coins_row['coin_id']);
                }
            }
        }

        // Update database to reflect newly spent coins since last update.
        $update_spent = $db->prepare("UPDATE coins SET spent = true WHERE coin_id = ?");
        $db->beginTransaction();
        foreach($spent as $coin) {
            $update_spent->bindValue(1, $coin, PDO::PARAM_INT);
            $update_spent->execute();
        }
        $db->commit();

        // Get the last chain from the coin.ini file and update the database with the new coins added (if any).
        $chains = getChains();
        $txids_stmt = $db->prepare('SELECT chain_id FROM txids WHERE txid = ?');
        $txids_stmt->execute([$chains['txid'][count($chains['txid']) - 1]]);
        $chain_id = $txids_stmt->fetch(PDO::FETCH_ASSOC);

        if(empty($chain_id)) { // If the chain is new then add it to the database as if a new build.
            loadChain($chains['txid'][count($chains['txid']) - 1], (int)$chains['frozen'][count($chains['txid']) - 1]);
        }else {
            $txids_stmt = $db->prepare('SELECT txid FROM txids WHERE chain_id = ? ORDER BY txid_id DESC LIMIT 1');
            $txids_stmt->bindValue(1, $chain_id['chain_id'],PDO::PARAM_INT);
            $txids_stmt->execute();
            $last_txid = $txids_stmt->fetch(PDO::FETCH_ASSOC);

            $txid_array = json_decode(file_get_contents(getExplorerAPI() . '/tx/' . $last_txid['txid']), true);
            $txs_array = json_decode(file_get_contents(getExplorerAPI() . '/address/' . $txid_array['vout'][0]['scriptpubkey_address'] . '/txs'), true);
            for($i = 0; $i < count($txs_array); $i++) {
                if($txs_array[$i]['vin'][0]['txid'] == $last_txid['txid']) {
                    loadData($chain_id, $txs_array[$i]['txid'], PHP_INT_MAX);
                }
            }
        }

        exit("Database update is complete!");
    }catch (PDOException $error) {
        echo "Error: " . $error->getMessage() . "<br>";
        echo "Line: " . $error->getLine() . "<br>";
        exit();
    }
}

// Load a new chain into the database. Uses the function loadData().
function loadChain(string $txid, int $depth) {
    global $db, $insert_chain;

    $frozen = true;
    if($depth == 0) {
        $depth = PHP_INT_MAX;
        $frozen = false;
    }else {
        $depth--;
    }

    // Extract OP_RETURN data for this chain
    $op_return = "";
    //$chain_tx = json_decode(file_get_contents(getExplorerAPI() . '/tx/' . $txid), true);
    if(empty($chain_tx = json_decode(file_get_contents(getExplorerAPI() . '/tx/' . $txid), true)))
        exit ("Error! Could not find the Chain's TXID in the Blockchain!");

    if($chain_tx["vout"][count($chain_tx["vout"]) - 1]['scriptpubkey_type'] == "op_return")
        $op_return = substr($chain_tx["vout"][count($chain_tx["vout"]) - 1]['scriptpubkey'], 4);

    // Store OP_RETURN and "frozen" status in the database.
    $insert_chain->bindValue(':op_return', $op_return, PDO::PARAM_STR);
    $insert_chain->bindValue(':frozen', $frozen, PDO::PARAM_BOOL);
    $insert_chain->execute();
    $last_chain_id = (int)$db->lastInsertId();

    // Load the database with all the coins in this chain.
    loadData($last_chain_id, $txid, $depth);
}

// Recursive function used to scale the given chain while loading each coin and txid into the database.
function loadData(int $chain_id, string $txid, int $depth) {
    global $db, $insert_txid, $insert_coin;

    $txid_array = json_decode(file_get_contents(getExplorerAPI() . '/tx/' . $txid), true);
    $txid_outspends = json_decode(file_get_contents(getExplorerAPI() . '/tx/' . $txid . '/outspends'), true);
    try {
        $db->beginTransaction();
        $insert_txid->bindValue(':chain_id', $chain_id, PDO::PARAM_INT);
        $insert_txid->bindValue(':txid', $txid, PDO::PARAM_STR);
        $insert_txid->execute();
        $last_txid_id = $db->lastInsertId();

        for($i = 1; $i < count($txid_array['vout']); $i++) {
            if($txid_array['vout'][$i]['scriptpubkey_type'] != "op_return" ) {
                $insert_coin->bindValue(':txid_id', $last_txid_id, PDO::PARAM_INT);
                $insert_coin->bindValue(':address', $txid_array['vout'][$i]['scriptpubkey_address'], PDO::PARAM_STR);
                $insert_coin->bindValue(':truncation', substr($txid_array['vout'][$i]['scriptpubkey_address'], 3, 9), PDO::PARAM_STR);
                $insert_coin->bindValue(':out', $i, PDO::PARAM_INT);
                $insert_coin->bindValue(':satoshis', $txid_array['vout'][$i]['value'], PDO::PARAM_INT);
                $insert_coin->bindValue(':spent', $txid_outspends[$i]['spent'], PDO::PARAM_BOOL);
                $insert_coin->execute();
            }
        }
        $db->commit();
    }catch (PDOException $error){
        $db->rollback();
        throw $error;
    }

    if($depth != 0) {
        $txs_array = json_decode(file_get_contents(getExplorerAPI() . '/address/' . $txid_array['vout'][0]['scriptpubkey_address'] . '/txs'), true);
        for($i = 0; $i < count($txs_array); $i++) {
            if($txs_array[$i]['vin'][0]['txid'] == $txid) {
                loadData($chain_id, $txs_array[$i]['txid'], $depth - 1);
            }
        }
    }
}
