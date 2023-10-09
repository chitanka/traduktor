<?php $this->pageTitle = "Напомняне на парола"; ?>

<h1>Склероза</h1>

<form method="post" action="/register/remind">
    <label for="clue">
        Въведете Вашия логин или e-mail, указани при регистрацията:
    </label>
    <input type="text" name="clue" id="clue" class="span6" autofocus
           value="<?php !isset($_POST["clue"]) ?: CHtml::encode($_POST["clue"]); ?>">
    <p>
        <button type="submit" class="btn btn-primary"><i class='icon-ok icon-white'></i> Възстановяване</button>
    </p>
</form>
