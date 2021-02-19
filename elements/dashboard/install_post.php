<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;
?>

<div class="alert alert-info" role="alert">
    <p>
        <?php
        echo t('
            Congrats, Bugsnag has been installed now.
            Please finish the integration by copying the API Key.
         ');
        ?>
    </p>
</div>


<a class="btn btn-primary" target="_blank" href="https://app.bugsnag.com/settings/projects">
    <?php echo t('1. Copy API Key') ?>
</a>
<br><br>

<a class="btn btn-primary" href="<?php echo Url::to('/dashboard/system/environment/bugsnag'); ?>">
    <?php echo t('2. Paste API Key') ?>
</a>
