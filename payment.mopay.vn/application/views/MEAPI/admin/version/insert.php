<?php echo $form; ?>
<?php echo $table; ?>
<script>
    $(function() {
        $("#game").combobox({
            _removeIfInvalid: function(event, ui) {
                alert('change');
            }
        });
        $("#game").on("autocompletechange", function(event, ui) {
            alert('change');
        });
        /* $( "#game" ).combobox({
         select: function( event, ui ) {
         alert('select');
         }
         }); */
    });
</script>