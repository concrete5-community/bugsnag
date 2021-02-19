<?php

namespace Concrete\Package\Bugsnag\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Monolog\Logger;

class Bugsnag extends DashboardPageController
{
    public function view()
    {
        $token = $this->app->make('token');

        $config = $this->app->make(Repository::class);
        $apiKey = $config->get('bugsnag.api_key', '');

        $this->set('token', $token);
        $this->set('apiKey', $apiKey);

        $this->set('logLevels', $this->getLogLevels());
        $this->set('logLevel', (int) $config->get('bugsnag.log_level'));
        $this->set('enableJavaScriptErrorDetection', (bool) $config->get('bugsnag.enable_javascript_error_detection'));
    }

    public function save()
    {
        $token = $this->app->make('token');
        if (!$token->validate('bugsnag_form')) {
            $this->flash('error', t('Invalid form token'));
            $this->redirect('/dashboard/system/environment/bugsnag');
        }

        $config = $this->app->make(Repository::class);

        $config->save('bugsnag.api_key', trim($this->post('apiKey')));
        $config->save('bugsnag.enable_javascript_error_detection', (bool) $this->post('enableJavaScriptErrorDetection'));

        if (array_key_exists($this->post('logLevel'), $this->getLogLevels())) {
            $config->save('bugsnag.log_level', (int) $this->post('logLevel'));
        }

        $this->flash('success', t('Your settings have been saved.'));
        $this->redirect('/dashboard/system/environment/bugsnag');
    }

    /**
     * @return array level codes => level names
     */
    protected function getLogLevels()
    {
        return array_flip(Logger::getLevels());
    }
}
