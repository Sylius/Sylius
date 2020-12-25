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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductAssociationContext implements Context
{
    /** @var EntityRepository */
    private $productAssociationRepository;

    /** @var ProductAssociationTypeRepositoryInterface */
    private $productAssociationTypeRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        EntityRepository $productAssociationRepository,
        ProductAssociationTypeRepositoryInterface $productAssociationTypeRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productAssociationRepository = $productAssociationRepository;
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @Transform /(?:product owner) "([^"]+)" .*. (?:association type) "([^"]+)"$/
     */
    public function getProductAssociationByProductOwnerAndType(string $productOwnerName, string $associationTypeName): ProductAssociationInterface
    {
        $productAssociationType = $this->productAssociationTypeRepository->findByName(
            $associationTypeName,
            'en_US'
        );

        $productOwner = $this->productRepository->findByName(
            $productOwnerName,
            'en_US'
        );

        $productAssociation = $this->productAssociationRepository->createQueryBuilder('p')
            ->leftJoin('p.owner', 'o')
            ->leftJoin('p.type', 't')
            ->where('o = :productOwner')
            ->setParameter('productOwner', $productOwner)
            ->andWhere('t = :associationType')
            ->setParameter('associationType', $productAssociationType)
            ->getQuery()
            ->getSingleResult()
        ;
        Assert::isInstanceOf($productAssociation, ProductAssociationInterface::class);

        return $productAssociation;
    }
}
