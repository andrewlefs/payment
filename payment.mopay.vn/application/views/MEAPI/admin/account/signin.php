<style>
    body{
        min-height: 500px;
    }
    .dynamic input{
        display:none;
    }
    .manager{
        display: none;
    }
    fieldset{
        width: 360px;
        position: absolute;
        top: 190px;
        left: 50%;
        margin-left: -187.5px;
    }
    fieldset p{
        font-size: 11px;
        font-family: Arial;
    }
    fieldset p span{
        float: left;
        margin-right: 4px;
    }
    fieldset input[type=text],fieldset input[type=password]{
        width: 174px !important;
    }
    fieldset .input_type_radio label{
        width: 70px !important;
    }
</style>
<script>
    $(function() {
        $('#username').focus();
        $('fieldset').prepend($('.manager').html());
    })
</script>
<fieldset>
    <legend>Đăng nhập</legend>
    <?php echo $form; ?>
</fieldset>
