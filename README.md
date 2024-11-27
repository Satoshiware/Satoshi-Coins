# Satoshi-Coins



NOTE: First Satoshi Coins' Prototype. Made with the Micro Bank of Queen Creek.in mind.



#Satoshi Coins
##The physical bitcoins you can hold




#Explorer
This repository contains an explorer that allows customers
search a database and find more details about a particular coin.

It runs on php, javascript, sqlite

When issuing commands in the Satoshi Coins' search box, make sure to use the web browser's incognito mode; otherwise,
// critical data (like the password) will be stored in the browser's history. If the password is forgotten, use ssh to
// erase the password hash in this file. Then the "passwd" command can be issued followed by the new password (no old password).
//
//
echo 'cmd: build $passwd<br>'; // Builds (or rebuilds) the database. The database is deleted (if present), coins.ini file is reloaded, and the blockchain is solicited for the desired data. // Potential Problems, coins.ini file non-existent has errors or does not have a password set.
echo 'cmd: update $passwd<br>'; // updates the database
echo 'cmd: password $new_passwd $old_passwd<br>'; // sets the password or changes it. If no password is set, Then previous password is anything. Can leave it blankwhatever it changes it too
echo 'cmd: upload $passwd<br>'; // Upload a new coins.ini file.
echo 'cmd: download $passwd<br>'; // download the coins.ini file.







// Get data example
//$sth = $db->prepare("SELECT rowid, address, truncation, value, spent, chain, txid, vout FROM coins");
//$sth->execute();
/* Fetch all of the remaining rows in the result set */
//$result = $sth->fetchAll();



// todo: We should make this part of our code to report all those modules that need installed.
//echo "<br>Does function exist?: " . function_exists('apcu_enabled');
//echo "<br>Does function exist?: " . function_exists("apcu_fetch");
//echo "<br>Does function exist?: " . function_exists("apcu_inc");
//echo "<br>Does function exist?: " . function_exists("apcu_delete");
//echo "<br>Does function exist?: " . function_exists("get_passwd_hash");



//Notes: software assumes one chain is frozen before another chain begins. Overlapping will not properly show coin order on what was produced first.








~~~~~~~~~~~~~~~~~~~~~~~ Scratch Pad ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
/////////////// Building the Database /////////////////////////
define("COINS_TABLE", "CREATE TABLE coins(
		chain UNSIGNED INT(2) NOT NULL,
		txid UNSIGNED INT(4) NOT NULL,
		address VARCHAR(42) NOT NULL,
		truncation VARCHAR(9) NOT NULL,
		out UNSIGNED INT(2) NOT NULL,
		satoshis UNSIGNED INT(4) NOT NULL,
		spent BOOLEAN NOT NULL);");
$db->exec(COINS_TABLE);


/////////////// Putting data into the Database /////////////////////////
$db->prepare("INSERT INTO chains (op_return, frozen) VALUES (:op_return, :frozen)");
            $data = [
                'op_return' => (string)$op_return,
                'frozen' => (boolean)$frozen];
            $insert_chain->execute($data);

/////////////// Updating data into the Database /////////////////////////

/////////////// Getting data from the Database (static) /////////////////////////
// We shouldn't use query, but rather a prepare,bind,and execute.
// We made an exception here because what is being extracted is static. no variables.
 $txids_stmt = $db->query('SELECT rowid, * FROM txids');
$txids_stmt->fetch(PDO::FETCH_ASSOC)

/////////////// Getting data from the Database (Non - static) /////////////////////////
$coins_stmt = $db->prepare('SELECT rowid, out, spent FROM coins WHERE txid = ?');
$coins_stmt->bindParam(1, $txids_row['rowid'], PDO::PARAM_INT);
$coins_stmt->execute();
while ($coins_row = $coins_stmt->fetch(PDO::FETCH_ASSOC))



With bindParam, the variable can be modified afterwards and then still be executed. "It is bound".
When passing values directly in execute all values are treated as strings (even if integer value is provided).
So if you need to enforce data types, you should always use bindValue or bindParam.
Note: The value of bindParam may change but the value of bindValue can not change.





so, we solicit the database for all unfrozen chains.
	We get there IDs and initial TXID
Now, what if some of those chains have become frozen?
	1) Well, we check each unfrozen chain to see if it has been frozen.
	2) We freeze it in the database.
	3) We calculate how many more tx's need processed.
we submit it to the the LoadData routine: If not frozen, with max supply. If frozen then with limited.






chains['txid']
chains['frozen']
chains['satoshi']
chains['note']

https://blockstream.info/api/tx/c3709e664bc6bf2d93d2eddaa715a7add8403365894826ede584191c3db1bad6




https://blockstream.info/api/address/bc1q7lmw4lcg9yzez5fn58pxtteqz5qzek7aqz5c5t/txs



/tx/c3709e664bc6bf2d93d2eddaa715a7add8403365894826ede584191c3db1bad6/outspend/:vout


https://blockstream.info/api/tx/c3709e664bc6bf2d93d2eddaa715a7add8403365894826ede584191c3db1bad6/outspends






