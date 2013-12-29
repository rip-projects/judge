<?php

/**
 * profile.php
 *
 * @package     arch-php
 * @author      xinixman <hello@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <hello@xinix.co.id>
 *
 *
 */

function _profile_group($value) {
    return $value['name'];
}

$excludes = array('password');
?>
<fieldset>
    <legend>User Profile</legend>
    <div class="right">
        <img src="<?php echo get_image_path('user/image/dsa.jpg') ?>" />
    </div>
    <?php foreach ($user as $key => $value): ?>
        <?php if (!in_array($key, $excludes)): ?>
            <div>
                <label><?php echo humanize($key) ?></label>

                <?php if (is_array($value)): ?>
                    <span>
                        <?php echo implode(', ', array_map('_profile_group', $value)) ?>
                    </span>
                <?php else: ?>
                    <span><?php echo $value ?></span>
                <?php endif ?>
            </div>
        <?php endif ?>
    <?php endforeach ?>
</fieldset>
