<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Positioner\PositionerInterface;
use Sylius\Component\Core\Repository\ProductTaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final readonly class UpdateProductTaxonPositionAction
{
    private const CSRF_TOKEN_NAME = 'update-product-taxon-position';

    public function __construct(
        private ProductTaxonRepositoryInterface $productTaxonRepository,
        private RequestStack $requestStack,
        private PositionerInterface $positioner,
        private ObjectManager $manager,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $csrfToken = new CsrfToken(self::CSRF_TOKEN_NAME, $data['_csrf_token'] ?? '');
        $this->validateCsrfProtection($csrfToken);

        $productTaxonPositions = $data['productTaxons'] ?? [];
        $productTaxonPositions = array_combine(
            array_column($productTaxonPositions, 'id'),
            array_column($productTaxonPositions, 'position'),
        );

        if (!$this->shouldProductsPositionsBeUpdated($request, $productTaxonPositions)) {
            return new JsonResponse();
        }

        $maxPosition = $this->getMaxPosition(array_keys($productTaxonPositions)[0]);

        try {
            $this->updatePositions($productTaxonPositions, $maxPosition);
        } catch (\InvalidArgumentException $exception) {
            FlashBagProvider::getFlashBag($this->requestStack)->add('error', $exception->getMessage());
        }

        return new JsonResponse();
    }

    private function validateCsrfProtection(CsrfToken $csrfToken): void
    {
        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }
    }

    /** @param array<int, string> $productTaxonPositions */
    private function updatePositions(array $productTaxonPositions, int $maxPosition): void
    {
        $modifiedProductTaxa = [];

        /** @var array<ProductTaxonInterface> $productTaxons */
        $productTaxons = $this->productTaxonRepository->findBy(['id' => array_keys($productTaxonPositions)]);

        foreach ($productTaxons as $productTaxon) {
            $newProductTaxonPosition = $productTaxonPositions[$productTaxon->getId()];
            if (!is_numeric($newProductTaxonPosition)) {
                throw new \InvalidArgumentException(sprintf('The position "%s" is invalid.', $newProductTaxonPosition));
            }

            $newProductTaxonPosition = (int) $newProductTaxonPosition;

            if (!$this->positioner->hasPositionChanged($productTaxon, $newProductTaxonPosition)) {
                continue;
            }

            $modifiedProductTaxa[] = [
                'productTaxon' => $productTaxon,
                'newPosition' => $newProductTaxonPosition,
            ];
        }

        foreach ($modifiedProductTaxa as $modifiedProductTaxon) {
            $this->positioner->updatePosition($modifiedProductTaxon['productTaxon'], $modifiedProductTaxon['newPosition'], $maxPosition);
            $this->manager->flush();
        }
    }

    /**
     * @param int[] $productTaxonPositions
     */
    private function shouldProductsPositionsBeUpdated(Request $request, ?array $productTaxonPositions): bool
    {
        return in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) &&
            null !== $productTaxonPositions &&
            [] !== $productTaxonPositions
        ;
    }

    private function getMaxPosition(mixed $productTaxonId): int
    {
        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $this->productTaxonRepository->find($productTaxonId);

        Assert::methodExists($this->productTaxonRepository, 'count');

        return $this->productTaxonRepository->count(['taxon' => $productTaxon->getTaxon()]) - 1;
    }
}
