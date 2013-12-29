<div class="header">
    <div class="left">
        <?php echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="right">
        <?php echo xform_anchor($CI->_get_uri('compose'), 'Compose', 'class="button"') ?>
    </div>
    <div class="clear"></div>
</div>

<div class="grid-top">
    <div class="left">
        <?php echo xform_anchor($CI->_get_uri('trash/inbox'), 'Trash', 'class="button mass-action"') ?>
    </div>
    <div class="right">
        <?php echo xview_filter($filter) ?>
    </div>
    <div class="clear"></div>
</div>

<?php echo $this->user_inbox->show($messages) ?>

<div class="grid-bottom">
    <?php echo $this->pagination->per_page_changer() ?>
    <?php echo $this->pagination->create_links() ?>
    <div class="clear"></div>
</div>
