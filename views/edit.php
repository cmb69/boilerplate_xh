<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $name
 * @var string $url
 * @var string $editorHeight
 * @var string $content
 * @var string $csrf_token_input
 */
?>
<!-- Boilerplate edit -->
<h1>Boilerplate – <?=$this->esc($name)?></h1>
<form class="plugineditform" action="<?=$this->esc($url)?>" method="post">
  <textarea class="plugintextarea" name="boilerplate_text" style="height: <?=$this->esc($editorHeight)?>px">
    <?=$this->esc($content)?>
  </textarea>
  <input type="hidden" name="admin" value="plugin_main">
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="boilerplate_name" value="<?=$this->esc($name)?>">
  <?=$this->raw($csrf_token_input)?>
</form>
