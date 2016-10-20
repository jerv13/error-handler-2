<?php

namespace RcmErrorHandler2\Log;

use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RcmErrorHandler\Log\ErrorNumberLogLevelMap;
use RcmErrorHandler2\Core\Config;
use RcmErrorHandler2\Exception\ErrorException;
use RcmErrorHandler2\Formatter\SummaryFormatter;
use RcmErrorHandler2\Formatter\TraceFormatter;
use RcmErrorHandler2\Observer\Observer;

/**
 * Class LoggerObserver
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   RcmErrorHandler\Log
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2014 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class LoggerObserver implements Observer
{
    /**
     * @var Config
     */
    protected $options;

    /**
     * @var SummaryFormatter
     */
    protected $summaryFormatter;

    /**
     * @var TraceFormatter
     */
    protected $traceFormatter;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * LoggerObserver constructor.
     *
     * @param Config             $options
     * @param SummaryFormatter   $summaryFormatter
     * @param TraceFormatter     $traceFormatter
     * @param ContainerInterface $container
     */
    public function __construct(
        Config $options,
        SummaryFormatter $summaryFormatter,
        TraceFormatter $traceFormatter,
        $container
    ) {
        $this->summaryFormatter = $summaryFormatter;
        $this->traceFormatter = $traceFormatter;
        $this->options = $options;
        $this->container = $container;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * notify
     *
     * @param ErrorException $errorException
     *
     * @return void
     */
    public function notify(ErrorException $errorException)
    {
        $loggerConfig = $this->options->get('loggers');

        $container = $this->container;

        $extra = $this->getExtras($errorException);

        $method = ErrorNumberLogLevelMap::getLogLevel($errorException->getSeverity());

        $message = $this->summaryFormatter->format($errorException);

        foreach ($loggerConfig as $serviceName) {
            /** @var LoggerInterface $logger */
            $logger = $container->get($serviceName);
            $logger->$method($message, $extra);
        }
    }

    /**
     * getExtras
     *
     * @param ErrorException $errorException
     *
     * @return array
     */
    protected function getExtras(ErrorException $errorException)
    {
        $extras = [
            'file' => $errorException->getFile(),
            'line' => $errorException->getLine(),
            'message' => $errorException->getMessage(),
            'exception' => $errorException->getActualExceptionClass(),
        ];

        if ($this->options->get('includeStacktrace', false) == true) {
            $extras['trace'] = $this->traceFormatter->format($errorException);
        }

        return $extras;
    }
}