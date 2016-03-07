<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\User\Repository\GroupRepositoryInterface;

/**
 * @author Antonio Perić <antonio@locastic.com>
 */
class GroupRepository extends EntityRepository implements GroupRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFormQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }
}
