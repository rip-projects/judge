<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(humanize(get_class($CI)))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri($CI->uri->rsegments[2]), 'title' => humanize(get_class($CI))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clear"></div>

<?php xview_error() ?>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <?php foreach ($field_data as $field): ?>
            <?php if (!$CI->_is_generated($field->name)): ?>
                <div>
                    <label><?php echo humanize($field->name) ?></label>
                    <?php if ($field->type == 'int'): ?>
                        <input type="text" value="<?php echo set_value($field->name) ?>" name="<?php echo $field->name ?>" class="number" <?php if (empty($next)) { $next = true; echo ' autofocus '; } ?> placeholder="<?php echo humanize($field->name) ?> (Number)" />
                    <?php else: ?>
                        <input type="text" value="<?php echo set_value($field->name) ?>" name="<?php echo $field->name ?>" <?php if (empty($next)) { $next = true; echo ' autofocus '; } ?> placeholder="<?php echo humanize($field->name) ?>" />
                    <?php endif ?>
                </div>
            <?php endif ?>
        <?php endforeach ?>
    </fieldset>
    <div class="action-buttons">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="button cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>