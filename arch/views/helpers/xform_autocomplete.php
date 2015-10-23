<?php $uniqid = uniqid('autocomplete_') ?>
<div id="<?php echo $uniqid ?>">
    <?php echo form_input($input_data, set_value($field), $extra) ?>
</div>
<script type="text/javascript">
    $(function() {
<?php if (is_string($data)): ?>
        var data = "<?php echo $data ?>";
        var options = {
            dataType: 'json',
            minChars: 0,
            max: 15,
            autoFill: true,
            mustMatch: true,
            matchContains: false,
            selectFirst: false,
            scrollHeight: 220,
            formatItem: function(row) {
                return row.join(' - ');
            },
            formatResult: function(row) {
                return row[0];
            }
        };
<?php else: ?>
        var data = <?php echo json_encode($data) ?>;
        var options = {
            minChars: 0,
            max: 15,
            autoFill: true,
            mustMatch: true,
            matchContains: false,
            scrollHeight: 220,
        };
<?php endif ?>
        $('#<?php echo $uniqid ?> input').autocomplete(data, options);
    });
</script>