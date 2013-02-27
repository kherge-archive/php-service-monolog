<?php

namespace Herrera\Service\Monolog\Tests;

use Herrera\PHPUnit\TestCase;
use Herrera\Service\Container;
use Herrera\Service\Monolog\MonologServiceProvider;

class MonologServiceProviderTest extends TestCase
{
    public function getHandlers()
    {
        return array(
            array(
                'handler.test',
                true,
                'Monolog\\Handler\\TestHandler'
            ),
            array(
                'handler.stream',
                STDOUT,
                'Monolog\\Handler\\StreamHandler'
            )
        );
    }

    public function getProcessors()
    {
        return array(
            array(
                'processor.introspection',
                'Monolog\\Processor\\IntrospectionProcessor'
            ),
            array(
                'processor.memory_peak_usage',
                'Monolog\\Processor\\MemoryPeakUsageProcessor'
            ),
            array(
                'processor.memory_usage',
                'Monolog\\Processor\\MemoryUsageProcessor'
            ),
            array(
                'processor.psr',
                'Monolog\\Processor\\PsrLogMessageProcessor'
            ),
            array(
                'processor.web',
                'Monolog\\Processor\\WebProcessor'
            )
        );
    }

    public function testRegister()
    {
        $container = new Container();
        $container->register(new MonologServiceProvider(), array());

        $this->assertInstanceOf('Monolog\\Logger', $container['monolog']);
        $this->assertSame(
            $container['monolog'],
            $container['monolog.loggers'][$container['monolog.default']]
        );
    }

    /**
     * @depends testRegister
     * @dataProvider getHandlers
     */
    public function testHandlers($param, $value, $class)
    {
        $container = new Container();
        $container->register(new MonologServiceProvider(), array(
            'monolog.options' => array(
                'default' => array($param => $value)
            )
        ));

        $this->assertInstanceOf(
            $class,
            $container['monolog.handlers'][$container['monolog.default']][0]
        );
    }

    /**
     * @depends testRegister
     * @dataProvider getProcessors
     */
    public function testProcessors($param, $class)
    {
        $container = new Container();
        $container->register(new MonologServiceProvider(), array(
            'monolog.options' => array(
                'default' => array($param => true)
            )
        ));

        $this->assertInstanceOf(
            $class,
            $container['monolog.processors'][$container['monolog.default']][0]
        );
    }

    /**
     * @depends testHandlers
     */
    public function testFunctional()
    {
        $container = new Container();
        $container->register(new MonologServiceProvider(), array(
            'monolog.options' => array(
                'default' => array(
                    'handler.test' => true
                )
            )
        ));

        $container['monolog']->debug('Test message.');

        $this->assertTrue(
            $container['monolog.handlers'][$container['monolog.default']][0]->hasDebug('Test message.')
        );
    }
}