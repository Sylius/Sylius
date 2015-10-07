<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
trait FilterManipulatorTrait {

    /**
     * Disables an ORM filter if it exists.
     *
     * @param $filter
     *
     * @return string
     */
    protected function disableFilter($filter)
    {
        $filters = $this->getEntityManager()->getFilters();

        if ($filters->isEnabled($filter)) {
            $filters->disable($filter);
        }
    }

    /**
     * Enables an ORM filter if it exists.
     *
     * @param $filter
     *
     * @return string
     */
    protected function enableFilter($filter)
    {
        $className = $this->getEntityManager()->getConfiguration()->getFilterClassName($filter);

        if (null !== $className) {
            $this->getEntityManager()->getFilters()->enable($filter);
        }
    }

    /**
     * @return EntityManagerInterface
     */
    abstract protected function getEntityManager();
}