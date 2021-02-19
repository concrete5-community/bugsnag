<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var $token \Concrete\Core\Validation\CSRF\Token */
/** @var $form \Concrete\Core\Form\Service\Form */
/** @var $apiKey string */
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php echo $token->output('bugsnag_form');?>

        <div class="form-group">
            <?php
            echo $form->label('apiKey', t('Bugsnag API Key').'*');
            echo $form->text('apiKey', $apiKey, [
                'autofocus' => 'autofocus',
                'maxlength' => 50,
            ]);
            ?>
        </div>

        <?php
        echo $form->submit('submit', t('Save'), ['class' => 'btn-primary']);
        ?>
    </form>
</div>
