<?php $id = uniqid('date_') ?>
<div id="<?php echo $id ?>" style="display: inline">
    <input type="text" class="date <?php echo @$options['class'] ?>" 
    	<?php echo (!empty($_POST[$name])) ? 'value="'. mysql_to_human($_POST[$name]) . '" data-value="' . @$_POST[$name] .'"' : '' ?>
    	<?php echo $extra ?> />
    <input type="hidden" class="hidden-val" value="<?php echo @$_POST[$name] ?>" name="<?php echo $name ?>" />
</div>