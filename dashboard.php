<?php
session_start();
require_once 'config.php';
require_once 'functions.php';
plansCron($_SESSION);

//calculate balance
$balance = getBalance($_SESSION);
$arr_withdrawals = get_withdrawals();
$total_withdrawal =  0;

foreach ($arr_withdrawals as $key => $value){
    $total_withdrawal +=  $value['amount'];
}

updateBalances($balance,$total_withdrawal);

$getUserBalance = getUserBalance();
$total_balance =  number_format($getUserBalance ,8,'.','');

$allplans = getPaidPlans();

//Active Plan
$active_plans = getActiveUserPlans($_SESSION['uid']);

//Calculate user earning rate
$userEarningRate = 0;
foreach($active_plans as $key => $value){
    $userEarningRate += $value->earning_rate;
}

?>
<div>
    <h4>Your balance</h4>
    <input type="hidden" id="getBalance" value="<?php echo $total_balance; ?>" />
    <span id="balance"><?php echo $total_balance; ?></span>
</div>

<div>
    <button type="button" onclick="location.href='logout.php';">Logout</button>
</div>

<h2>Your Active Plan</h2>
<table>
    <tbody>
        <tr>
            <th>Name</th>
            <th>Speed</th>
            <th>Earning Rate</th>
            <th>Start</th>
            <th>Time left</th>
            <th>Status</th>
        </tr>
        <?php
            $sumCD = 0; $sumER = 0; $sumSP = 0;
            foreach($active_plans as $key => $plans):
                $sumER += $plans->earning_rate; $sumSP += $plans->speed; $sumCD += $plans->point_per_day;
                $duration = $plans->duration;
                if($duration == 0) {
                    $leftDays = 'Unlimited';
                }else{
                    $now = date_create('now');
                    $end = date_add(date_create($plans->created_at),date_interval_create_from_date_string($duration.' days'));
                    $left = date_diff($now,$end);
                    $leftDays = $left->days.'d '.$left->h.'h '.$left->i. 'min';
                }
            ?>
        <tr>
            <td><?php echo $plans->version; ?></td>
            <td><?php echo $plans->speed; ?> H/s</td>
            <td><?php echo $plans->earning_rate; ?> <?php echo CURSYM;?></td>
            <td><?php echo $plans->created_at; ?></td>
            <td><?php echo $leftDays; ?></td>
            <td><?php echo $plans->status; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><b>Totals</b></td>
            <td><?php echo $sumSP;?> H/s</td>
            <td><?php echo currencyFormat($sumER);?> <?php echo CURSYM;?> min / <?php echo currencyFormat($sumCD);?> <?php echo CURSYM;?> day</td>
        </tr>
    </tfoot>
</table>

<p>Unix time: <?php echo time();?></p>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
//Counter
$(document).ready(function() {
    var speed = (parseFloat(<?php echo $userEarningRate;?>)/60).toFixed(8);
    setInterval(function() {
        var oldvalue =  parseFloat($('#balance').html()).toFixed(8);
        var result = parseFloat(parseFloat(oldvalue) + parseFloat(speed)).toFixed(8);
        $("#balance").html(result);
    }, 1000);
});
</script>