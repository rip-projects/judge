<ul class="menu">
    <?php foreach ($menus as $menu): ?>
    <li <?php echo (empty($menu['children'])) ? '' : ' class="has-children" ' ?>>
        <a href="<?php echo (!empty($menu['uri'])) ? site_url($menu['uri']) : site_url(@$menu['children'][0]['uri']) ?>"><?php echo l($menu['title']) ?></a>
            <?php if (!empty($menu['children'])): ?>
                <?php echo $self->_get_menu($menu['children']); ?>
            <?php endif ?>
    </li>
    <?php endforeach ?>
</ul>