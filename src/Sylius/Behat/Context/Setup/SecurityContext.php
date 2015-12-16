<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Sylius\Behat\Context\FeatureContext;
use Sylius\Bundle\CoreBundle\Test\Services\SecurityServiceInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityContext extends FeatureContext
{
    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(SharedStorageInterface $sharedStorage, SecurityServiceInterface $securityService)
    {
        $this->securityService = $securityService;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->securityService->logIn($email, 'main', $this->getSession());
    }
}
