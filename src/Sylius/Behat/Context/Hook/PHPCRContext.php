<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Doctrine\Bundle\PHPCRBundle\Initializer\InitializerManager;
use Doctrine\Common\DataFixtures\Purger\PHPCRPurger;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PHPCRContext implements Context
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @var InitializerManager
     */
    private $initializerManager;

    /**
     * @param DocumentManagerInterface $documentManager
     * @param InitializerManager $initializerManager
     */
    public function __construct(DocumentManagerInterface $documentManager, InitializerManager $initializerManager)
    {
        $this->documentManager = $documentManager;
        $this->initializerManager = $initializerManager;
    }

    /**
     * @BeforeScenario
     */
    public function purgeAndInitializeDatabase()
    {
        $purger = new PHPCRPurger($this->documentManager);
        $purger->purge();

        $this->documentManager->clear();

        $this->initializerManager->initialize();
    }
}
