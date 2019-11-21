<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\AvatarImage;

final class AvatarRepository extends EntityRepository
{
    public function findOneByOwner(string $id): ?AvatarImage
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.owner = :user')
            ->andWhere('user.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }
}
