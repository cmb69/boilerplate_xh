<?php
if (!isset($this)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
?>
<!-- Boilerplate_XH: info -->
<h1>Boilerplate <?=$version?></h1>
<h4><?=$this->text('syscheck_title')?></h4>
<?php foreach ($checks as $check) :?>
<div><?=$check?></div>
<?php endforeach?>
