<?php

namespace A3020\Bugsnag\Logging\Handler;

use Bugsnag\Report;
use Exception;
use Monolog\Handler\AbstractProcessingHandler;

class BugsnagHandler extends AbstractProcessingHandler
{
    /** @var \Bugsnag\Client */
    protected $bugsnag;

    public function setBugsnag($bugsnag)
    {
        $this->bugsnag = $bugsnag;
    }

    protected function write(array $record)
    {
        if (!$this->bugsnag) {
            throw new Exception(t('Bugsnag client isn\'t set.'));
        }

        $report = Report::fromNamedError(
            $this->bugsnag->getConfig(),
            $record['channel'],
            $record['formatted']
        );

        $this->bugsnag->notify($report);
    }
}
