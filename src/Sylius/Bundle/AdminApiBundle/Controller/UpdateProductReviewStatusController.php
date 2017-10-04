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

namespace Sylius\Bundle\AdminApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Paul Stoica <paul.stoica18@gmail.com>
 */
final class UpdateProductReviewStatusController
{
    /**
     * @var ProductReviewRepositoryInterface
     */
    private $productReviewRepository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param RepositoryInterface $productReviewRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        RepositoryInterface $productReviewRepository,
        EntityManagerInterface $manager
    ) {
        $this->productReviewRepository = $productReviewRepository;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function acceptProductReviewAction(Request $request): JsonResponse
    {
        $reviewId = $request->get('id');
        $productCode = $request->get('productCode');

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && (null === $reviewId || null === $productCode)) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        /** @var ReviewInterface $productReview */
        $productReview = $this->productReviewRepository->findOneByIdAndProductCode(
            $reviewId,
            $productCode
        );

        $productReview->setStatus(ReviewInterface::STATUS_ACCEPTED);

        $this->manager->persist($productReview);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function rejectProductReviewAction(Request $request): JsonResponse
    {
        $reviewId = $request->get('id');
        $productCode = $request->get('productCode');

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && (null === $reviewId || null === $productCode)) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        /** @var ReviewInterface $productReview */
        $productReview = $this->productReviewRepository->findOneByIdAndProductCode(
            $reviewId,
            $productCode
        );

        $productReview->setStatus(ReviewInterface::STATUS_REJECTED);

        $this->manager->persist($productReview);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
