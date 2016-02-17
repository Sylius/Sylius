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
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class HookContext extends DefaultContext
{
    /**
     * @BeforeScenario
     */
    public function purgeDatabase(BeforeScenarioScope $scope)
    {
        $entityManager = $this->getService('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $isMySqlDriver = $entityManager->getConnection()->getDriver() instanceof PDOMySqlDriver;
        if ($isMySqlDriver) {
            $entityManager->getConnection()->executeUpdate('SET foreign_key_checks = 0;');
        }

        $this->getSharedService('sylius.purger.orm_purger')->purge();

        if ($isMySqlDriver) {
            $entityManager->getConnection()->executeUpdate('SET foreign_key_checks = 1;');
        }

        $entityManager->clear();
    }
}
