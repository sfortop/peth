<?php
/**
 * zumpay-billing
 *
 * @author Serhii Borodai <clarifying@gmail.com>
 */

namespace Infrastructure\Factory;


use Infrastructure\Exception\InvalidConfigException;
use Proxy\Container;
use Psr\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Log\PsrLoggerAdapter;
use Zend\Log\Writer\Factory\WriterFactory;
use Zend\Log\Writer\Stream;

class LoggerFactory
{
    /**
     * @var WriterFactory
     */
    private $writerFactory;

    /**
     * LoggerFactory constructor.
     */
    public function __construct()
    {
        $this->writerFactory = new WriterFactory();
    }


    /**
     * @param ContainerInterface $container
     * @return PsrLoggerAdapter
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');

        $logger = new Logger();

        if (isset($config['log']['writers']) && $logger instanceof Logger) {
            foreach ($config['log']['writers'] as $writer) {
                $writerName = $writer['name'] ?? Stream::class;
                if (!isset($writer['options'])) {
                    $options = ['stream' => 'php://stdout'];
                } else {
                    $options = $writer['options'];
                }
                $this->writerFactory->setCreationOptions($options);
                $writer = $this->writerFactory->createService($container->get(Container::class), $writerName);
                $logger->addWriter($writer);
            }
        }

        if (isset($config['log']['exceptionhandler']) && $config['log']['exceptionhandler']) {
            Logger::registerExceptionHandler($logger);
        }
        if (isset($config['log']['errorhandler']) && $config['log']['errorhandler']) {
            Logger::registerErrorHandler($logger);
        }
        if (isset($config['log']['fatal_error_shutdownfunction']) && $config['log']['fatal_error_shutdownfunction']) {
            Logger::registerFatalErrorShutdownFunction($logger);
        }

        return new PsrLoggerAdapter($logger);
    }


}