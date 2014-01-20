<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Behat;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Sylius main feature context.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FeatureContext implements KernelAwareContext
{
    /**
     * Parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase()
    {
        $entityManager = $this->getService('doctrine.orm.entity_manager');

        $purger = new ORMPurger($entityManager);
        $purger->purge();
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        die(var_dump($kernel->getEnvironment()));
        // @todo this is some ugly hack =)
        $kernel->boot();

        $this->kernel = $kernel;
    }

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->kernel->getContainer()->get($id);
    }
}
