<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $version
 * @var list<string> $checks
 */
?>
<!-- Boilerplate info -->
<h1>Boilerplate <?=$this->esc($version)?></h1>
<h4><?=$this->text('syscheck_title')?></h4>
<?foreach ($checks as $check):?>
<?=$this->raw($check)?>
<?endforeach?>
