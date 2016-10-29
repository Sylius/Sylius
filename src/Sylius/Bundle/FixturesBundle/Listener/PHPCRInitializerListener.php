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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerManager;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PHPCRInitializerListener extends AbstractListener implements BeforeSuiteListenerInterface
{
    /**
     * @var InitializerManager
     */
    private $initializerManager;

    /**
     * @param InitializerManager $initializerManager
     */
    public function __construct(InitializerManager $initializerManager)
    {
        $this->initializerManager = $initializerManager;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSuite(SuiteEvent $suiteEvent, array $options)
    {
        $this->initializerManager->initialize();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'phpcr_initializer';
    }
}
