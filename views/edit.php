<?php

use Boilerplate\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $name
 * @var string $url
 * @var string $editorHeight
 * @var string $content
 */
?>
<!-- Boilerplate_XH: edit -->
<h1>Boilerplate – <?=$name?></h1>
<form class="plugineditform" action="<?=$url?>" method="post">
    <textarea class="plugintextarea" name="boilerplate_text" style="height: <?=$editorHeight?>px">
        <?=$content?>
    </textarea>
    <input type="hidden" name="admin" value="plugin_main">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="boilerplate_name" value="<?=$name?>">
    <?=$this->csrfToken()?>
</form>
