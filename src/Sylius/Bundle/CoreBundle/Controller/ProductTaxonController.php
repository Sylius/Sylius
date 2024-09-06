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

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Positioner\PositionerInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductTaxonController extends ResourceController
{
    public function updateProductTaxonsPositionsAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->validateCsrfProtection($request, $configuration);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);

        $productTaxonsPositions = $request->request->all('productTaxons');
        $productTaxonsPositions = array_combine(
            array_column($productTaxonsPositions, 'id'),
            array_column($productTaxonsPositions, 'position'),
        );

        if (!$this->shouldProductsPositionsBeUpdated($request, $productTaxonsPositions)) {
            return new JsonResponse();
        }

        $maxPosition = $this->getMaxPosition(array_keys($productTaxonsPositions)[0]);

        try {
            $this->updatePositions($productTaxonsPositions, $maxPosition);
        } catch (\InvalidArgumentException $exception) {
            /** @var Session $session */
            $session = $this->getMainRequest()->getSession();
            $session->getFlashBag()->add('error', $exception->getMessage());
        }

        return new JsonResponse();
    }

    /** @param array<int, string> $productTaxonPositions */
    private function updatePositions(array $productTaxonPositions, int $maxPosition): void
    {
        $positioner = $this->getPositioner();
        $modifiedProductTaxons = [];

        /** @var array<ProductTaxonInterface> $productTaxons */
        $productTaxons = $this->repository->findBy(['id' => array_keys($productTaxonPositions)]);

        foreach ($productTaxons as $productTaxon) {
            $newProductTaxonPosition = $productTaxonPositions[$productTaxon->getId()];
            if (!is_numeric($newProductTaxonPosition)) {
                throw new \InvalidArgumentException(sprintf('The position "%s" is invalid.', $newProductTaxonPosition));
            }

            $newProductTaxonPosition = (int) $newProductTaxonPosition;

            if (!$positioner->hasPositionChanged($productTaxon, $newProductTaxonPosition)) {
                continue;
            }

            $modifiedProductTaxons[] = [
                'productTaxon' => $productTaxon,
                'newPosition' => $newProductTaxonPosition,
            ];
        }

        foreach ($modifiedProductTaxons as $modifiedProductTaxon) {
            $positioner->updatePosition($modifiedProductTaxon['productTaxon'], $modifiedProductTaxon['newPosition'], $maxPosition);
            $this->manager->flush();
        }
    }

    private function getMaxPosition(mixed $productTaxonId): int
    {
        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $this->repository->find($productTaxonId);

        /** @phpstan-ignore-next-line */
        return $this->repository->count(['taxon' => $productTaxon->getTaxon()]) - 1;
    }

    private function validateCsrfProtection(Request $request, RequestConfiguration $configuration): void
    {
        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid('update-product-taxon-position', (string) $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }
    }

    /** @param array<string, int>|null $productTaxons */
    private function shouldProductsPositionsBeUpdated(Request $request, ?array $productTaxons): bool
    {
        return in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) &&
            null !== $productTaxons &&
            [] !== $productTaxons
        ;
    }

    private function getPositioner(): PositionerInterface
    {
        /** @var PositionerInterface $positioner */
        $positioner = $this->get(PositionerInterface::class);

        return $positioner;
    }

    private function getMainRequest(): Request
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->get('request_stack');

        return $requestStack->getMainRequest();
    }
}
