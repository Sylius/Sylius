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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class HookContext implements Context, KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

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
    public function purgeDatabase(BeforeScenarioScope $scope)
    {
        $entityManager = $this->getService('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $isMySqlDriver = $entityManager->getConnection()->getDriver() instanceof PDOMySqlDriver;
        if ($isMySqlDriver) {
            $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 0;");
        }

        $purger = new ORMPurger($entityManager);
        $purger->purge();

        if ($isMySqlDriver) {
            $entityManager->getConnection()->executeUpdate("SET foreign_key_checks = 1;");
        }

        $entityManager->clear();

        /*
        $process = new Process(sprintf('%s/console sylius:rbac:initialize --env=test', $this->getContainer()->getParameter('kernel.root_dir')));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Could not initialize permissions.');
        }
        */
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