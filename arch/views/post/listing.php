<div class="header">
    <div class="left">
        <?php echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="right">
        <?php //echo xform_anchor($CI->_get_uri('import') . '/csv', 'Import', 'class="button"') ?>
        <?php echo xform_anchor($CI->_get_uri('add'), 'Add', 'class="button"') ?>
    </div>
    <div class="clear"></div>
</div>

<div class="grid-top">
    <div class="left">
        <?php echo xform_anchor($CI->_get_uri('delete'), 'Delete', 'class="button mass-action"') ?>
    </div>
    <div class="right">
        <?php
        $arr = (empty($filter['tag'])) ? array() : array($filter['tag']);
        $extra = form_dropdown('tag', $tag_options, $arr, 'id="tag-select"');
        $extra .=
                "<script>
                        $(function() {
                            $('#tag-select').change(function() {
                                $(this).parents('form').submit();
                            });
                        });
                        </script>";
        ?>
        <?php echo xview_filter($filter, $extra) ?>
    </div>
    <div class="clear"></div>
</div>

<?php echo $this->listing_grid->show($items) ?>

<?php if (!$this->input->is_ajax_request()): ?>
    <div class="grid-bottom">
        <?php echo $this->pagination->per_page_changer() ?>
        <?php echo $this->pagination->create_links() ?>
        <div class="clear"></div>
    </div>
<?php endif ?>