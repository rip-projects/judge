<?php echo $this->admin_panel->breadcrumb(array(
array( 'uri' => $CI->_get_uri('listing'), 'title' => humanize(get_class($CI)) ),
array( 'uri' => $CI->uri->uri_string, 'title' => 'Send to Trash'),
)) ?>
<div class="clear"></div>

<fieldset>
    <legend>Confirmation</legend>
    
    <div>
        <label>&nbsp;</label>
        <span><?php if (count($ids) > 1): ?>
        Are you sure want to <strong style="color:#c00">send to trash</strong> <?php echo count($ids) ?> records?
        <?php else: ?>
        Are you sure want to <strong style="color:#c00">send to trash</strong> record #<?php echo $id ?>?
        <?php endif ?></span>
    </div>
</fieldset>

<div class="action-buttons">
    <a href="<?php echo site_url($CI->_get_uri('trash').'/'.$from.'/'.$id.'?confirmed=1') ?>" class="button"><?php echo l('OK') ?></a>
    <a href="<?php echo site_url($CI->_get_uri($from)) ?>" class="button cancel"><?php echo l('Cancel') ?></a>
</div>