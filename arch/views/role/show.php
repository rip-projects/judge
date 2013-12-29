<?php $title = l((empty($id) ? 'Add %s' : 'Edit %s'), array(humanize(get_class($CI)))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri($CI->uri->rsegments[2]), 'title' => humanize(get_class($CI))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clear"></div>

<?php echo xview_error() ?>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label class="mandatory"><?php echo l('Name') ?></label>
            <input type="text" value="<?php echo set_value('name') ?>" name="name" placeholder="Role name" />
        </div>
    </fieldset>
    <div class="action-buttons">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="button cancel"><?php echo l('Cancel') ?></a>
    </div>
</form>

<?php if (!empty($id)): ?>
    <div style="margin-top: 10px;">
        <form action="<?php echo site_url('role/add_privilege/' . $id) ?>" method="post" class="ajaxform">
            <fieldset>
                <legend>Privileges</legend>
                <div>
                    <input type="text" name="uri" />
                    <input type="submit" value="Add" />
                </div>
                <?php echo $this->grid_privilege->show($privileges); ?>
            </fieldset>
        </form>
    </div>

<?php endif ?>