<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Indexer;

use Doctrine\ORM\EntityManager;

/**
 * Interface IndexerInterface
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
interface IndexerInterface
{
    /**
     * Populates the index table.
     *
     * Entity manager is needed by orm indexer but not for es indexer. It exists
     * here to overcome a circular reference error on the orm side. There was a solution
     * by using events but ends up creating more complicated code than it suppose to be.
     *
     * @param EntityManager $em
     *
     * @return mixed
     */
    public function populate(EntityManager $em);

    /**
     * Gathers the command output for informing the user
     *
     * @return string
     */
    public function getOutput();
}
