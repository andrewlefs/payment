<!DOCTYPE html>
<html>
<head>
    <title>Mopay.vn</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
    <link rel="stylesheet" href="assets/css/loading.css" type="text/css"/>
</head>
<body>
<?php
$CI =& get_instance();
$CI->load->MEAPI_Library('Language');
$CI->Language->init($language);

if (empty($unit) == TRUE) {
    $unit = 'mcoin';
}
?>
<?php
if ($status == TRUE) {
    ?>

    <div class="content-popup result-bank-success">
        <div class="title-1"><?php echo $CI->Language->item('WEB_BANKING_PAY_SUCCESS', array('money' => $money)); ?>
        </div>


        <div class="name"><?php echo $mobo_id; ?></div>
        <div class="balance"><?php echo $CI->Language->item('WEB_BANKING_RECEIVED'); ?><span
                class="credit_value"><?php echo $credit; ?></span> <?php echo $unit; ?>
        </div>
        <?php
        if (is_numeric($balance)) {
            ?>
            <div class="balance"><?php echo $CI->Language->item('WEB_BANKING_BALANCE'); ?><span
                    class="balance_value"><?php echo $balance; ?></span> <?php echo $unit; ?>
            </div>
        <?php
        }
        ?>
        <div class="aws"><?php echo $CI->Language->item('WEB_BANKING_CONTINUE'); ?></div>
        <div class="hrr"></div>
        <?php
        if (empty($callback) === TRUE) {
            ?>
            <a href="http://mopay.vn">
                <button
                    class="button-close"><?php echo $CI->Language->item('WEB_BANKING_PAY_BUTTON', array('unit' => $unit)); ?></button>
            </a>
        <?php
        } else {
            ?>
            <a href="<?php echo $callback; ?>">
                <button
                    class="button-close"><?php echo $CI->Language->item('WEB_BANKING_PAY_BUTTON', array('unit' => $unit)); ?></button>
            </a>
        <?php
        }
        ?>

    </div>

<?php
} else {
    ?>
    <div class="content-popup result-bank-success">
        <div class="title-1"><?php echo $message; ?></div>
        <div class="aws"><?php echo $CI->Language->item('WEB_BANKING_CONTINUE'); ?></div>
        <div class="hrr"></div>
        <?php
        if (empty($callback) === TRUE) {
            ?>
            <a href="http://mopay.vn">
                <button
                    class="button-close"><?php echo $CI->Language->item('WEB_BANKING_PAY_BUTTON', array('unit' => $unit)); ?></button>
            </a>
        <?php
        } else {
            ?>
            <a href="<?php echo $callback; ?>">
                <button
                    class="button-close"><?php echo $CI->Language->item('WEB_BANKING_PAY_BUTTON', array('unit' => $unit)); ?></button>
            </a>
        <?php
        }
        ?>
    </div>
<?php
}
?>
</body>
</html>