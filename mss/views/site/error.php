<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.11111111111111111111
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>
    <?php var_dump($_SERVER);?>
</div>
<script type='text/javascripte'>
document.write(window.location.href);
</script>
