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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\Repository\Repository;

/**
 * UserGroup rule configuration form type.
 *
 * @author Antonio Perić <antonio@locastic.com>
 */
class GroupRepository extends Repository
{
    public function getFormQueryBuilder()
    {
        return $this->getCollectionQueryBuilder();
    }
}
