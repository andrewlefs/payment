<?php echo $form; ?>
<?php echo $table; ?>
<script>
    $(function() {
        $("#game").combobox({
            change: function(event, ui) {
                alert('444');
            }
        });
    });
</script>