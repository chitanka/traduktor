<?php
	/**
	* @var User $user
	* @var string $h1 = $user->login
	* @var string $html_insert1 = ""
	*/
    // ! ЗАГЛУШКА !
    $h1 = '';
    $html_insert1 = "";
?>
<div class="row profile-header">

<div class="span1 profile-header-upic">
<?php
	echo "<img src='{$user->upicUrl}' width='50' height='50' alt='' class='upic";
		if(is_array($user->upic) && $user->upic[0]) {
			echo " active' data-upic='" . $user->id . "." . join(".", $user->upic);
		}
	echo "' />";

	if(Yii::app()->user->id == $user->id) {
		echo "<a href='#upic-modal' data-toggle='modal' title='Смяна на аватара'>смяна</a>";
	}
?>
</div>

<div class="span7">

<h1><?php echo $user->login . ($h1 != "" ? ": {$h1}" : ""); ?></h1>
<?=$html_insert1; ?>
<ul class="nav nav-tabs">
<?php
	foreach($this->submenu as $action => $label) {
		echo "<li" . ($this->action->id == $action ? " class='active'" : "") . "><a href='" . $user->getUrl($action) . "'>{$label}</a></li>";
	}
?>
</ul>

</div>

</div>

<?php if(Yii::app()->user->id == $user->id): ?>
<div id="upic-modal" class="modal hide">
    <form method="post" enctype="multipart/form-data" class="form-inline" action="<?=$user->getUrl("upic"); ?>">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">×</a>
            <h3>Смяна на аватара</h3>
        </div>
        <div class="modal-body">
            <p>Аватарът е вашето второ лице. Той се показва в профила ви и до всеки ваш коментар. Хората го асоциират с вас.</p>
			<p>Моля, изберете файл във формат JPEG, PNG или GIF, не по-голем от 2 мегабайта.</p>
			<p class="i"><input type="file" name="img" /></p>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Качване</button>
        </div>
    </form>
</div>
<?php endif; ?>
