<pre>
About Framework
===============
<?php foreach ($framework as $key => $value): ?>
<?php echo humanize($key).':'.$value."\n"; ?>
<?php endforeach ?>
</pre>

<br/>
<br/>
<br/>
<a href="<?php echo site_url('user/login') ?>">Login</a>
