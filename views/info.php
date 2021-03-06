<?php
if (!isset($this)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}
?>
<!-- Boilerplate_XH: info -->
<h1>Boilerplate_XH &ndash; <?=$this->text('label_info')?></h1>
<h4><?=$this->text('syscheck_title')?></h4>
<?php foreach ($checks as $check) :?>
<div><?=$check?></div>
<?php endforeach?>
<hr>
<h4><?=$this->text('about')?></h4>
<img src="<?=$icon?>" style="float: left; width: 128px; height: 128px; margin-right: 16px"
    alt="<?=$this->text('alt_logo')?>">
<p>Version: <?=$version?></p>
<p>Copyright &copy; 2012-2021 <a href="http://3-magi.net/">Christoph M. Becker</a></p>
<p style="text-align: justify">
    This program is free software: you can
    redistribute it and/or modify it under the terms of the GNU General Public
    License as published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.
</p>
<p style="text-align: justify">
    This program is distributed in the hope that it
    will be useful, but <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See the GNU General
    Public License for more details.
</p>
<p style="text-align: justify">
    You should have received a copy of the GNU
    General Public License along with this program. If not, see
    <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
