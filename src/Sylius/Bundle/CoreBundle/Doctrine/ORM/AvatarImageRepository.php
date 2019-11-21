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
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;

final class AvatarImageRepository extends EntityRepository implements AvatarImageRepositoryInterface
{
    public function findOneByOwnerId(string $ownerId): ?ImageInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
