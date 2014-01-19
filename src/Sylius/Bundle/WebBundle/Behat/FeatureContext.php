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

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Sylius main feature context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FeatureContext extends RawMinkContext implements KernelAwareInterface
{
    /**
     * Kernel.
     *
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * Parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;

        // Web user context.
        $this->useContext('web-user', new WebUser());

        Request::enableHttpMethodParameterOverride();
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase()
    {
        $entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');

        $purger = new ORMPurger($entityManager);
        $purger->purge();
    }

    /**
     * @Given /^I remove property choice number (\d+)$/
     */
    public function iRemovePropertyChoiceInput($number)
    {
        $this
            ->getSession()
            ->getPage()
            ->find('css', sprintf('.sylius_property_choices_%d_delete', $number))
            ->click()
        ;
    }
}
