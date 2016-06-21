<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LoggerListener extends AbstractListener implements BeforeSuiteListenerInterface, BeforeFixtureListenerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSuite(SuiteEvent $suiteEvent, array $options)
    {
        $this->logger->notice(sprintf('Running suite "%s"...', $suiteEvent->suite()->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function beforeFixture(FixtureEvent $fixtureEvent, array $options)
    {
        $this->logger->notice(sprintf('Running fixture "%s"...', $fixtureEvent->fixture()->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'logger';
    }
}
