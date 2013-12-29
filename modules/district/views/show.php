<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(humanize(get_class($CI)))) ?>
<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri($CI->uri->rsegments[1]), 'title' => humanize(get_class($CI))),
))
?>

<div class="clear"></div>
<form action="" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title; ?></legend>
        <div>
            <div>
                <label class="mandatory"><?php echo l('Name') ?></label>
                <input type="text" value="<?php echo set_value('name') ?>" name="name"  />
            </div>
            <div>
                <label class="mandatory"><?php echo l('City') ?></label>
                <?php echo form_dropdown('city_id', $city_options) ?>
            </div>
        </div>
    </fieldset>
    <div class="action-buttons">
        <input type="submit" value="<?php echo l('Save') ?>"/>
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="button cancel"><?php echo l('Back') ?></a>
    </div>
</form>
