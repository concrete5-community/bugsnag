<?php

defined('C5_EXECUTE') or die('Access Denied.');

/** @var $token \Concrete\Core\Validation\CSRF\Token */
/** @var $form \Concrete\Core\Form\Service\Form */
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php echo $token->output('bugsnag_form');?>

        <div class="form-group">
            <?php
            /** @var $apiKey string */
            echo $form->label('apiKey', t('Bugsnag API Key').'*');
            echo $form->text('apiKey', $apiKey, [
                'autofocus' => 'autofocus',
                'maxlength' => 50,
            ]);
            ?>
        </div>

        <div class="form-group">
            <?php
            /** @var $logLevels array */
            /** @var $logLevel string */
            echo $form->label('logLevel', t('Send logs to Bugsnag only if their level is at least').'*');
            echo $form->select('logLevel', $logLevels, $logLevel);
            ?>
        </div>

        <div class="form-group well">
            <label>
                <?php
                /** @var $enableJavaScriptErrorDetection bool */
                echo $form->checkbox('enableJavaScriptErrorDetection', 1, $enableJavaScriptErrorDetection);
                ?>
                <?php echo t('Enable JavaScript error detection'); ?>
            </label>

            <p style="margin-bottom: 0;">
                <?php
                echo t('Automatically detect JavaScript errors on your websites, collect detailed diagnostic information, and get notified about errors in real-time.');
                ?><br>
                <?php
                echo t('By enabling JavaScript error detection, a single script tag will be added to your HTML output.');
                ?>
            </p>
        </div>

        <?php
        echo $form->submit('submit', t('Save'), ['class' => 'btn-primary']);
        ?>
    </form>
</div>
