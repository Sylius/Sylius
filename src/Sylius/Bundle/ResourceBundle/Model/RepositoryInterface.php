<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Model;

/**
 * Model repository interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RepositoryInterface
{
    public function createNew();
    public function find($id);
    public function findAll();
    public function findOneBy(array $criteria);
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
    public function createPaginator(array $criteria = null, array $orderBy = null);
    public function getPaginator($queryBuilder);
}
