<?php

namespace RcmErrorHandler2\Factory;

use Interop\Container\ContainerInterface;
use RcmErrorHandler2\Config\RcmErrorHandler2Config;
use RcmErrorHandler2\Service\PhpErrorOverride;

/**
 * Class ServicePhpErrorOverrideFactory
 *
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2016 Reliv International
 * @license   License.txt
 * @link      https://github.com/reliv
 */
class ServicePhpErrorOverrideFactory
{
    /**
     * __invoke
     *
     * @param ContainerInterface $container
     *
     * @return PhpErrorOverride
     */
    public function __invoke($container)
    {
        $config = $container->get(RcmErrorHandler2Config::class);
        return new PhpErrorOverride(
            $container,
            $config
        );
    }
}
