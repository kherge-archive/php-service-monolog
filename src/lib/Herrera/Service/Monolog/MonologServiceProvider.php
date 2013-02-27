<?php

namespace Herrera\Service\Monolog;

use Herrera\Service\Container;
use Herrera\Service\ProviderInterface;
use Monolog\Handler;
use Monolog\Logger;
use Monolog\Processor;

/**
 * Registers a Logger instance with the service container.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class MonologServiceProvider implements ProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Container $container)
    {
        $container['monolog.default'] = 'default';
        $container['monolog.defaults'] = array(
            'bubble' => true,
            'handler.test' => false,
            'handler.stream' => null,
            'handlers' => array(),
            'level' => Logger::DEBUG,
            'name' => 'default',
            'processor.introspection' => false,
            'processor.memory_peak_usage' => false,
            'processor.memory_usage' => false,
            'processor.psr' => false,
            'processor.web' => false,
            'processors' => array()
        );

        $container['monolog.options'] = array(
            $container['monolog.default'] => $container['monolog.defaults']
        );

        $container['monolog'] = $container->once(
            function (Container $container) {
                return $container['monolog.loggers'][$container['monolog.default']];
            }
        );

        $container['monolog.loggers'] = $container->once(
            function (Container $container) {
                $loggers = array();

                foreach ($container['monolog.options'] as $name => $options) {
                    $loggers[$name] = new Logger(
                        $options['name'],
                        $container['monolog.handlers'][$name],
                        $container['monolog.processors'][$name]
                    );
                }

                return $loggers;
            }
        );

        $container['monolog.handlers'] = $container->once(
            function (Container $container) {
                $handlers = array();

                foreach ($container['monolog.options'] as $name => $options) {
                    $handlers[$name] = array();

                    if (false === empty($options['handler.test'])) {
                        $handlers[$name][] = new Handler\TestHandler(
                            $options['level'],
                            $options['bubble']
                        );
                    }

                    if (isset($options['handler.stream'])) {
                        $handlers[$name][] = new Handler\StreamHandler(
                            $options['handler.stream'],
                            $options['level'],
                            $options['bubble']
                        );
                    }
                }

                return $handlers;
            }
        );

        $container['monolog.processors'] = $container->once(
            function (Container $container) {
                $processors = array();

                foreach ($container['monolog.options'] as $name => $options) {
                    $processors[$name] = array();

                    if (false === empty($options['processor.introspection'])) {
                        $processors[$name][] = new Processor\IntrospectionProcessor();
                    }

                    if (false === empty($options['processor.memory_peak_usage'])) {
                        $processors[$name][] = new Processor\MemoryPeakUsageProcessor();
                    }

                    if (false === empty($options['processor.memory_usage'])) {
                        $processors[$name][] = new Processor\MemoryUsageProcessor();
                    }

                    if (false === empty($options['processor.psr'])) {
                        $processors[$name][] = new Processor\PsrLogMessageProcessor();
                    }

                    if (false === empty($options['processor.web'])) {
                        $processors[$name][] = new Processor\WebProcessor();
                    }
                }

                return $processors;
            }
        );
    }
}
