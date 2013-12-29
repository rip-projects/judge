<?php if (!$this->input->is_ajax_request()): ?>
    <div class="header">
        <div class="left">
            <?php echo $this->admin_panel->breadcrumb() ?>
        </div>
        <div class="right">
            <?php echo xform_anchor($CI->_get_uri('add'), 'Add', 'class="button"') ?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="grid-top">
        <div class="left">
            <?php echo xform_anchor($CI->_get_uri('delete'), 'Delete', 'class="button mass-action"') ?>
        </div>
        <div class="right">
            
        </div>
        <div class="clear"></div>
    </div>
<?php endif ?>

<?php echo $this->listing_grid->show($items) ?>

<?php if (!$this->input->is_ajax_request()): ?>
    <div class="grid-bottom">
        <?php echo $this->pagination->per_page_changer() ?>
        <?php echo $this->pagination->create_links() ?>
        <div class="clear"></div>
    </div>
<?php endif ?>
