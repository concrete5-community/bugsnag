<?php

namespace Concrete\Package\Bugsnag;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Exception;

class Controller extends Package
{
    protected $pkgHandle = 'bugsnag';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.0';

    public function getPackageName()
    {
        return t('Bugsnag Integration');
    }

    public function getPackageDescription()
    {
        return t('Detects and sends logs to Bugsnag.');
    }

    public function on_start()
    {
        $config = $this->app->make(Repository::class);
        $apiKey = $config->get('bugsnag.api_key');

        if (empty($apiKey)) {
            return;
        }

        try {
            require_once __DIR__."/vendor/autoload.php";

            /** @var $bugsnag \Bugsnag\Client */
            $bugsnag = \Bugsnag\Client::make($apiKey);
            \Bugsnag\Handler::register($bugsnag);

            Events::addListener('on_exception', function($event) use($bugsnag) {
                $bugsnag->notifyException($event->getSubject());
            });
        } catch (Exception $e) {
            $log = $this->app->make('log/exceptions');
            $log->addError('Bugsnag: '. $e->getMessage());
        }
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installPage($pkg);
    }

    public function uninstall()
    {
        parent::uninstall();

        $config = $this->app->make(Repository::class);
        $config->clear('bugsnag.api_key');
    }

    public function upgrade()
    {
        $pkg = PackageFacade::getByHandle($this->pkgHandle);
        $this->installPage($pkg);
    }

    private function installPage($pkg)
    {
        $path = '/dashboard/system/environment/bugsnag';

        $page = Page::getByPath($path);
        if ($page && !$page->isError()) {
            return;
        }

        $singlePage = Single::add($path, $pkg);
        $singlePage->update('Bugsnag');
    }
}
