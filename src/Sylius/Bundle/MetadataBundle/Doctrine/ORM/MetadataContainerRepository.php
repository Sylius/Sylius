<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      24/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Metadata\Repository\MetadataContainerRepositoryInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class MetadataContainerRepository extends EntityRepository implements MetadataContainerRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByTypeAndCode($type, $code)
    {
        return $this->createQueryBuilder('o')
            ->where('o.type = :type')
            ->andWhere('o.code = :code')
            ->setParameter('type', $type)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}