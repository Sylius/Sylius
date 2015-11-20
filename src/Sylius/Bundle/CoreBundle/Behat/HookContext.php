<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Behat;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class HookContext extends DefaultContext
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var PurgerInterface
     */
    private static $ormPurger;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeStep
     */
    public function setTimeouts(BeforeStepScope $scope)
    {
        $driver = $this->getMink()->getSession()->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $driver->setTimeouts(['page load' => 30000]);
        }
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase(BeforeScenarioScope $scope)
    {
        $entityManager = $this->getService('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $isMySqlDriver = $entityManager->getConnection()->getDriver() instanceof PDOMySqlDriver;
        if ($isMySqlDriver) {
            $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 0;");
        }

        if (null === self::$ormPurger) {
            self::$ormPurger = $this->getService('sylius.purger.orm_purger');
        }

        self::$ormPurger->purge();

        if ($isMySqlDriver) {
            $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 1;");
        }

        $entityManager->clear();
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
        return $this->getContainer()->get($id);
    }

    /**
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }
} 
