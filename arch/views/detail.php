<?php $title = humanize(get_class($CI)). ' Detail' ?>
<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => humanize(get_class($CI))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>

<div class="clear"></div>
<fieldset>
    <legend><?php echo $title ?></legend>
    <?php foreach ($field_data as $field): ?>
        <?php if (!$CI->_is_generated($field->name)): ?>
            <div class="clear" style="min-height: 30px;">
                <label><?php echo humanize($field->name) ?></label>
                <span><?php echo $data[$field->name] ?></span>
            </div>
        <?php endif ?>
    <?php endforeach ?>
</fieldset>

<div class="action-buttons">
    <a href="javascript:history.back()" class="button cancel"><?php echo l('Back') ?></a>
</div>
