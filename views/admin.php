<?php

use Boilerplate\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $url
 * @var list<array{editURL:string,deleteURL:string}> $boilerplates
 * @var string $csrf_token_input
 */
?>
<!-- Boilerplate_XH: administration -->
<h1>Boilerplate – <?=$this->text('menu_main')?></h1>
<div id="boilerplate_admin" data-delete-confirmation="<?=$this->text("confirm_delete")?>">
    <table>
<?php foreach ($boilerplates as $name => $boilerplate) :?>
        <tr>
            <td>
                <form action="<?=$url?>" method="post" onsubmit="return confirm(document.getElementById('boilerplate_admin').dataset.deleteConfirmation);">
                    <button><?=$this->text('label_delete')?></button>
                    <input type="hidden" name="admin" value="plugin_main">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="boilerplate_name" value="<?=$name?>">
                    <?=$csrf_token_input?>
                </form>
            </td>
            <td>
                <a href="<?=$boilerplate['editURL']?>" title="<?=$this->text('label_edit')?>"><?=$name?></a>
            </td>
            <td>
                <input type="text" readonly="readonly" value="{{{boilerplate('<?=$name?>')}}}"
                    onclick="this.select();">
            </td>
        </tr>
<?php endforeach?>
    </table>
    <form action="<?=$url?>" method="post">
        <input type="text" name="boilerplate_name">
        <input type="hidden" name="admin" value="plugin_main">
        <input type="hidden" name="action" value="new">
        <?=$csrf_token_input?>
        <input type="submit" class="submit" value="<?=$this->text('label_create')?>">
    </form>
</div>
