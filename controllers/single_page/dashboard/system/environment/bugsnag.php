<?php

namespace Concrete\Package\Bugsnag\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;

class Bugsnag extends DashboardPageController
{
    public function view()
    {
        $token = $this->app->make('token');

        $config = $this->app->make(Repository::class);
        $apiKey = $config->get('bugsnag.api_key', '');

        $this->set('token', $token);
        $this->set('apiKey', $apiKey);
    }

    public function save()
    {
        $token = $this->app->make('token');
        if (!$token->validate('bugsnag_form')) {
            $this->flash('error', t('Invalid form token'));
            $this->redirect('/dashboard/system/environment/bugsnag');
        }

        $config = $this->app->make(Repository::class);
        $config->save('bugsnag.api_key', trim($this->request('apiKey')));

        $this->flash('success', t('API key has been updated.'));
        $this->redirect('/dashboard/system/environment/bugsnag');
    }
}
