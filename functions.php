<?php
$timezone = date_default_timezone_get();

//Get user balance
function getBalance(array $data): string {
    global $conn;

    $stmt = $conn->prepare("
        SELECT uph.id, uph.plan_id, uph.user_id, uph.last_sum, uph.created_at, p.earning_rate
        FROM user_plan_history uph
        INNER JOIN plans p ON uph.plan_id = p.id
        WHERE uph.user_id = :userid AND status = 'active'
    ");
    $stmt->bindParam(':userid', $data['uid']);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $earning = 0;
    if ($res) {
        foreach ($res as $val) {
            $date1 = time();
            $date2 = $val['last_sum'] ?: strtotime($val['created_at']);
            $sec = $date1 - $date2;
            $earning += $sec * ($val['earning_rate'] / 60);

            $updateStmt = $conn->prepare("UPDATE user_plan_history SET last_sum = :last WHERE id = :id");
            $updateStmt->bindParam(':id', $val['id']);
            $updateStmt->bindValue(':last', time());
            $updateStmt->execute();
        }
    }

    return number_format($earning, 8, '.', '');
}
//Create user
function createUser(string $username, int $reference_user_id, string $user_ip_addr): void {
    global $conn;

    $unique_id = random_int(10000, 99999);

    $stmt = $conn->prepare("SELECT p.id FROM plans p WHERE is_default = 1");
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("
        INSERT INTO users (username, plan_id, reference_user_id, ip_addr, unique_id)
        VALUES (:un, :pid, :ref_id, :ip_addr, :unique_id)
    ");
    $stmt->bindParam(':un', $username);
    $stmt->bindParam(':pid', $res['id']);
    $stmt->bindParam(':ref_id', $reference_user_id);
    $stmt->bindParam(':ip_addr', $user_ip_addr);
    $stmt->bindParam(':unique_id', $unique_id);
    $stmt->execute();

    $uid = $conn->lastInsertId();
    $stmt = $conn->prepare("
        INSERT INTO user_plan_history (user_id, plan_id, status, created_at) 
        VALUES (:uid, :pid, 'active', :date)
    ");
    $stmt->bindParam(':date', date('Y-m-d H:i:s'));
    $stmt->bindParam(':uid', $uid);
    $stmt->bindParam(':pid', $res['id']);
    $stmt->execute();
}
//Get user data
function getUser(string $username): array|false {
    global $conn;

    $stmt = $conn->prepare("
        SELECT u.*, p.*, u.id AS uid
        FROM users u
        INNER JOIN plans p ON u.plan_id = p.id
        WHERE u.username = :username
    ");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    return $res;
}
//Get user withdraws
function get_withdrawals($type='payment',$status=null)
{
	$res_arr = [];
	if(isset($_SESSION['uid']) && $_SESSION['uid'] != "")
	{
		global $conn;
		if($status){
            $stmt = $conn->prepare("SELECT * FROM user_withdrawal WHERE user_id = :user AND type = :type AND status=:status");
        }else{
            $stmt = $conn->prepare("SELECT * FROM user_withdrawal WHERE user_id = :user AND type = :type");
        }
        if($status){
            $stmt->bindParam(':status', $status);
        }
		$stmt->bindParam(':user', $_SESSION['uid']);
		$stmt->bindParam(':type', $type);
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		// $res = $stmt->fetch();

		while ($res = $stmt->fetch()) 
		{
		    $res_arr[]= $res;
		}
		return $res_arr;
	}
}
//Get real IP from user
function getRealIpAddr() {
    return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
}
//List only paid plans
function getPaidPlans() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM plans WHERE is_default=0");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $res = $stmt->fetchAll();
    return $res;
}
// Disable expired plans
function plansCron(array $data): void {
    global $conn;

    $stmt = $conn->prepare("
        SELECT *
        FROM user_plan_history
        WHERE user_id = :userid AND status = 'active' AND expire_date IS NOT NULL
    ");
    $stmt->bindParam(':userid', $data['uid']);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($res as $re) {
        $now = time();
        $expire_date = strtotime($re['expire_date']);

        if ($now >= $expire_date) {
            $stmt = $conn->prepare("UPDATE user_plan_history SET status = 'inactive' WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $re['id']);
            $stmt->execute();
        }
    }
}
//Get user active plans
function getUserAcPlans($userId){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user_plan_history uh INNER JOIN plans p ON p.id=uh.plan_id  WHERE user_id= :userId AND status='active'");
    $stmt->bindParam('userId',$userId);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_OBJ);
    return $stmt->fetchAll();
}
//Create user transaction
function createTransaction($user,$plan,$amount,$hash){
    global $conn;
    $stmt = $conn->prepare("INSERT into transactions_history (user_id, plan_id,amount,hash) VALUES (:id, :plan,:amo,:hash)");
    $stmt->bindParam(':id', $user);
    $stmt->bindParam(':plan', $plan);
    $stmt->bindParam(':amo', $amount);
    $stmt->bindParam(':hash', $hash);
    $stmt->execute();
}
function updateBalances($balance,$withdraws)
{
	global $conn;
	$stmt = $conn->prepare("UPDATE users SET balance = balance+:bal, cashouts = cashouts+:with WHERE id = :id LIMIT 1");
	$stmt->bindParam(':id', $_SESSION['uid']);
	$stmt->bindParam(':bal', $balance);
	$stmt->bindParam(':with', $withdraws);
	$stmt->execute();
}
function getUserBalance()
{
	global $conn;
	$stmt = $conn->prepare("SELECT balance FROM users WHERE id = :id LIMIT 1");
	$stmt->bindParam(':id', $_SESSION['uid']);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_OBJ);
	$bal = $stmt->fetchColumn();
	return $bal;
}
function debitUserBalance($amount)
{
    if(isset($amount)){
        global $conn;
        $stmt = $conn->prepare("UPDATE users SET balance = balance-:amount, cashouts = cashouts+:withdraw WHERE id = :id LIMIT 1");
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':withdraw', $amount);
        $stmt->bindParam(':id', $_SESSION['uid']);
        $stmt->execute();
    }
}