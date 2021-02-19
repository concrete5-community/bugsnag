<?php

namespace Concrete\Package\Bugsnag;

use A3020\Bugsnag\Logging\Handler\BugsnagHandler;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Concrete\Core\Support\Facade\Events;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\GenericEvent;

class Controller extends Package
{
    protected $pkgHandle = 'bugsnag';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.3';
    protected $pkgAutoloaderRegistries = [
        'src/Bugsnag' => '\A3020\Bugsnag',
    ];

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

            $this->registerListeners($bugsnag, $config);
        } catch (Exception $e) {
            $log = $this->app->make('log/exceptions');
            $log->addError('Bugsnag: '. $e->getMessage());
        }
    }

    /**
     * @param $bugsnag \Bugsnag\Client
     * @param Repository $config
     */
    private function registerListeners($bugsnag, $config)
    {
        Events::addListener('on_exception', function($event) use($bugsnag) {
            $bugsnag->notifyException($event->getSubject());
        });

        $handler = $this->getBugsnagHandler($config->get('bugsnag.log_level'));
        $handler->setBugsnag($bugsnag);

        Events::addListener('on_logger_create', function($event) use ($handler) {
            /** @var \Concrete\Core\Logging\Event $event */
            $logger = $event->getLogger();
            $logger->pushHandler($handler);

            return $logger;
        });

        if ($config->get('bugsnag.enable_javascript_error_detection')) {
            $apiKey = $bugsnag->getConfig()->getApiKey();
            Events::addListener('on_header_required_ready', function($event) use ($apiKey) {
                /** @var GenericEvent $event */
                $linkTags = $event->getArgument('linkTags');
                $linkTags[] = '<script src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-3.min.js"'.
                    'data-apikey="'.$apiKey.'"></script>';
                $event->setArgument('linkTags', $linkTags);
            });
        }
    }

    /**
     * @param int $logLevel The minimum logging level at which this handler will be triggered
     * @return BugsnagHandler
     */
    private function getBugsnagHandler($logLevel = Logger::DEBUG)
    {
        /** @var BugsnagHandler $handler */
        $handler = new BugsnagHandler($logLevel);
        $output = "%message%";
        $formatter = new LineFormatter($output, null, true);
        $handler->setFormatter($formatter);

        return $handler;
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installPage($pkg);

        $config = $this->app->make(Repository::class);
        $config->save('bugsnag.log_level', Logger::ERROR);
    }

    public function uninstall()
    {
        parent::uninstall();

        $config = $this->app->make(Repository::class);
        $config->clear('bugsnag.api_key');
        $config->clear('bugsnag.log_level');
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
