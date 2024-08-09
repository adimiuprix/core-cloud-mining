<?php

require_once 'config.php';
require_once 'functions.php';

session_start();

plansCron($_SESSION);

if (isset($_SESSION['id'])) {
    header('Location: dashboard.php');
}

if (isset($_GET['refer']) && !empty($_GET['refer'])) {
    $reference_user_id = (int)$_GET['refer'];
    if (is_int($reference_user_id)) {
        $_SESSION['reference_id'] = $reference_user_id;
    }
}

if (isset($_POST['username'])) {
    $reference_user_id = !empty($_POST['reference_user_id']) ? $_POST['reference_user_id'] : 0;

    $username = trim(strip_tags($_POST['username']));
    $res = getUser($username);
    $user_ip_addr = getRealIpAddr();

    if ($res) {
        $_SESSION = $res;
    } else {
        createUser($username, $reference_user_id, $user_ip_addr);
        $res = getUser($username);
        $_SESSION = $res;
    }
    echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';
}

?>

<form action="<?php BASE_PATH;?>index.php" method="post">
    <input type="hidden" name="reference_user_id"  value="<?php echo (isset($reference_user_id))?$reference_user_id:""; ?>">
    <input type="text" id="username" minlength="<?php echo WALLET_MINCH;?>" maxlength="<?php echo WALLET_MAXCH;?>" pattern="[a-zA-Z0-9_-]+" name="username" placeholder="Enter Your <?php echo CURNAME;?> Address" />
    <button class="but-hover" id="go_enter" onclick="return validateFormLogin()">Start mining</button>
</form>

<p>Core Cloud Mining</p>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script type="text/javascript">
    function validateFormLogin(){
        var min_length = <?php echo WALLET_MINCH;?>;
        var max_length = <?php echo WALLET_MAXCH;?>;
        var error_message = "";

        var val_length = $("#username").val().length;
        if(val_length > 0)
        {
            if(val_length <  min_length ){
                    error_message = "Wallet salah, masukkan dengan alamat address crypto!";
                    $("#result").html(error_message);
                    return false;
            }
            if(val_length  > max_length){
                error_message = "Alamat kepanjangan!";
                $("#result").html(error_message);
                return false;
            }
            success_message = "Tunggu ya, lagi di proses....";
            $("#result").text(success_message);
            return true;
        }else{
            error_message = "Harap di isi...";
            $("#result").text(error_message);
            return false;
        }
    }
</script>