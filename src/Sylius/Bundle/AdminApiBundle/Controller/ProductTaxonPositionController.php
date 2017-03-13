<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Repository\ProductTaxonRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonPositionController
{
    /**
     * @var ProductTaxonRepositoryInterface
     */
    private $productTaxonRepository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param RepositoryInterface $productTaxonRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        RepositoryInterface $productTaxonRepository,
        EntityManagerInterface $manager
    ) {
        $this->productTaxonRepository = $productTaxonRepository;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updatePositionsAction(Request $request)
    {
        $productsPositions = $request->request->get('productsPositions');

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null === $productsPositions) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        foreach ($productsPositions as $positionData) {
            if (!is_numeric($positionData['position'])) {
                throw new HttpException(
                    Response::HTTP_BAD_REQUEST,
                    sprintf('The productTaxon position "%s" is invalid.', $positionData['position'])
                );
            }

            /** @var ProductTaxonInterface $productTaxonFromBase */
            $productTaxonFromBase = $this->productTaxonRepository->findOneByProductCodeAndTaxonCode(
                $positionData['productCode'],
                $request->attributes->get('taxonCode')
            );

            $productTaxonFromBase->setPosition($positionData['position']);

            $this->manager->flush();
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
