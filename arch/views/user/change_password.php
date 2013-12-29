<?php $title = l('Change Password') ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clear"></div>

<?php echo xview_error() ?>
<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label><?php echo l('Old Password') ?></label>
            <input type="password" value="<?php echo set_value('old_password') ?>" name="old_password"  />
        </div>
        <div>
            <label><?php echo l('New Password') ?></label>
            <input type="password" value="<?php echo set_value('password') ?>" name="password"  />
        </div>
        <div>
            <label><?php echo l('Re-type New Password') ?></label>
            <input type="password" value="<?php echo set_value('password2') ?>" name="password2"  />
        </div>
    </fieldset>
    <div class="action-buttons">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('')) ?>" class="button cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>