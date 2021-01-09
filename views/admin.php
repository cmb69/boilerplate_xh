<?php
if (!isset($this)) {
	header('HTTP/1.0 404 Not Found');
	exit;
}
?>
<!-- Boilerplate_XH: administration -->
<h1>Boilerplate – <?=$this->text('menu_main')?></h1>
<div id="boilerplate_admin">
    <table>
<?php foreach ($boilerplates as $name => $boilerplate):?>
	<tr>
	    <td>
		<form action="<?=$url?>" method="post" onsubmit="return confirm('<?=$confirmation?>');">
		    <input type="image" src="<?=$deleteImage?>" alt="<?=$this->text('label_delete')?>" title="<?=$this->text('label_delete')?>"/>
		    <input type="hidden" name="admin" value="plugin_main"/>
		    <input type="hidden" name="action" value="delete"/>
		    <input type="hidden" name="boilerplate_name" value="<?=$name?>"/>
		    <?=$this->csrfToken()?>
		</form>
	    </td>
	    <td>
		<a href="<?=$boilerplate['editURL']?>" title="<?=$this->text('label_edit')?>"><?=$name?></a>
	    </td>
	    <td>
		<input type="text" readonly="readonly" value="{{{boilerplate('<?=$name?>')}}}" onclick="this.select();"/>
	    </td>
	</tr>
<?php endforeach;?>
    </table>
    <form action="<?=$url?>" method="post">
	<input type="text" name="boilerplate_name"/>
	<input type="hidden" name="admin" value="plugin_main"/>
	<input type="hidden" name="action" value="new"/>
	<?=$this->csrfToken()?>
	<input type="submit" class="submit" value="<?=$this->text('label_create')?>"/>
    </form>
</div>
