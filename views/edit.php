<?php
if (!isset($this)) {
	header('HTTP/1.0 404 Not Found');
	exit;
}
?>
<!-- Boilerplate_XH: edit -->
<h1>Boilerplate – <?=$name?></h1>
    <form class="plugineditform" action="<?=$url?>" method="post">
        <textarea class="plugintextarea" name="boilerplate_text" style="height: <?=$editorHeight?>px"><?=$content?></textarea>
        <input type="hidden" name="admin" value="plugin_main"/>
        <input type="hidden" name="action" value="save"/>
        <input type="hidden" name="boilerplate_name" value="<?=$name?>"/>
        <?=$this->csrfToken()?>
    </form>
</div>
