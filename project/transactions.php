<?php
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(!isset($_GET["type"])){
	echo "You must pass a query param ?type=  with deposit, withdraw, or transfer";
	die();
}
function getWorldAccount(){
	$db = getDB();
	$stmt = $db->prepare("SELECT id from Accounts where account_number = '000000000000'");
	$r = $stmt->execute();
	if(!$r){
		echo var_export($stmt->errorInfo(), true);
		return -1;
	}
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	return (int)$result["id"];
}
function getAccountBalance($account){
	$balance = 0;
	//get the SUM of the change from accounts for the acct_src_id ($account)
	//https://www.w3schools.com/sql/func_mysql_ifnull.asp
	return $balance;
}
function updateBalance($account){
	//update Account balance = SUM of the change from accounts for the acct_src id where account id is $account
	//doesn't return anything
}
function do_bank_action($account1, $account2, $amountChange, $type, $memo = ""){
	if($account1 <= 0 || $account2 <=0){
		echo "Invalid account id";
		return;
	}
	$db = getDB();
	$a1total = getAccountBalance($account1);//TODO get total of account 1
	$a2total = getAccountBalance($account2);//TODO get total of account 2
	$a1total += $amountChange;
	$a2total -= $amountChange;
	$query = "INSERT INTO `Transactions` (`AccountSource`, `AccountDest`, `Amount`, `Type`, `Total`) 
	VALUES(:p1a1, :p1a2, :p1change, :type, :a1total), 
			(:p2a1, :p2a2, :p2change, :type, :a2total)";
	
	$stmt = $db->prepare($query);
	$stmt->bindValue(":p1a1", $account1);
	$stmt->bindValue(":p1a2", $account2);
	$stmt->bindValue(":p1change", $amountChange);
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":a1total", $a1total);
	//flip data for other half of transaction
	$stmt->bindValue(":p2a1", $account2);
	$stmt->bindValue(":p2a2", $account1);
	$stmt->bindValue(":p2change", ($amountChange*-1));
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":a2total", $a2total);
	$result = $stmt->execute();
	if($result){
		updateBalance($account1);
		updateBalance($account2);
	}
	else{
		echo var_export($stmt->errorInfo(), true);
	}
}
?>
<form method="POST">
	<!-- make a dropdown of (id, account_number) from the current user's accounts-->
	<input type="text" name="account1" placeholder="Account Id">
	<!-- If our sample is a transfer show other account field-->
	<?php if($_GET['type'] == 'transfer') : ?>
	<input type="text" name="account2" placeholder="Other Account Id">
	<?php endif; ?>
	
	<input type="number" name="amount" placeholder="$0.00"/>
	<input type="hidden" name="type" value="<?php echo $_GET['type'];?>"/>
	
	<!--Based on sample type change the submit button display-->
	<input type="submit" value="Move Money"/>
</form>

<?php
if(isset($_POST['type']) && isset($_POST['account1']) && isset($_POST['amount'])){
	$type = $_POST['type'];
	$amount = (int)$_POST['amount'];
	
	switch($type){
		case 'deposit':
			do_bank_action(getWorldAccount(), $_POST['account1'], ($amount * -1), $type);
			break;
		case 'withdraw':
			do_bank_action($_POST['account1'], getWorldAccount(), ($amount * -1), $type);
			break;
		case 'transfer':
			//TODO similar to withdraw but note how deposit works
			break;
	}
}
?>
