<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UpdateProductTaxonPosition
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $productTaxonRepository;

    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    private $manager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        RepositoryInterface $productTaxonRepository,
        TaxonRepositoryInterface $taxonRepository,
        EntityManager $manager
    ) {
        $this->productRepository = $productRepository;
        $this->productTaxonRepository = $productTaxonRepository;
        $this->taxonRepository = $taxonRepository;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updatePositionsAction(Request $request)
    {
        $positionsData = $request->get('positionsData');

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null !== $positionsData) {

            foreach ($positionsData as $positionData) {
                if (!is_numeric($positionData['position'])) {
                    throw new HttpException(
                        Response::HTTP_BAD_REQUEST,
                        sprintf('The productTaxon position "%s" is invalid.', $positionData['position'])
                    );
                }
                /** @var ProductInterface $product */
                $product = $this->productRepository->findOneBy(['code' => $positionData['productCode']]);
                /** @var TaxonInterface $taxon */
                $taxon = $this->taxonRepository->findOneBy(['code' => $request->get('taxonCode')]);

                /** @var ProductTaxonInterface $productTaxonFromBase */
                $productTaxonFromBase = $this->productTaxonRepository->findOneBy(['product' => $product->getId(), 'taxon' => $taxon->getId()]);
                $productTaxonFromBase->setPosition($positionData['position']);

                $this->manager->persist($productTaxonFromBase);
            }

            $this->manager->flush();

        }

        return new JsonResponse();
    }
}
