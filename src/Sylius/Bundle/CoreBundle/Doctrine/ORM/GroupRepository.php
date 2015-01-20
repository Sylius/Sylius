<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * UserGroup rule configuration form type.
 *
 * @author Antonio Perić <antonio@locastic.com>
 */
class GroupRepository extends EntityRepository
{
    public function getFormQueryBuilder()
    {
        return $this->getCollectionQueryBuilder();
    }
}
