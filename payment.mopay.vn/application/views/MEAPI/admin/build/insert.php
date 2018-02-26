<script>
    $(function() {
        $("input[name='build']").click(function() {
            showConfig();
        })
        $("input[name='platform']").click(function() {
            showConfig();
        })
        showConfig();
    })
    function showConfig()
    {
        if ($("input[name='build']:checked").val() == 1)
        {
            if ($("input[name='platform']:checked").length == 0)
                alert('Vui lòng chọn platform để build');
            else
            {
                $(".div_control.group").hide();
                $(".div_control.group." + $("input[name='platform']:checked").val()).show();
            }
        }
        else
        {
            $(".div_control.group").hide();
        }
    }
</script>
<style>
    .div_control.group{
        display: none; 
    }
    .div_control.red label{
        color:red;
    }
</style>
<?php
echo $form?>