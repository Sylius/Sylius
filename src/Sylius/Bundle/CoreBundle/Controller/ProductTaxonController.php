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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Positioner\PositionerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductTaxonController extends ResourceController
{
    /**
     * @throws HttpException
     *
     * @deprecated This ajax action is deprecated and will be removed in Sylius 2.0 - use ProductTaxonController::updateProductTaxonsPositionsAction instead.
     *
     * @psalm-suppress DeprecatedMethod
     */
    public function updatePositionsAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxons = $this->getParameterFromRequest($request, 'productTaxons');
        $this->validateCsrfProtection($request, $configuration);

        if ($this->shouldProductsPositionsBeUpdated($request, $productTaxons)) {
            /** @psalm-var array{position: string|int, id: int} $productTaxon */
            foreach ($productTaxons as $productTaxon) {
                try {
                    $this->updatePositions($productTaxon['position'], $productTaxon['id']);
                } catch (\InvalidArgumentException $exception) {
                    throw new HttpException(Response::HTTP_BAD_REQUEST, $exception->getMessage());
                }
            }
        }

        return new JsonResponse();
    }

    public function updateProductTaxonsPositionsAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->validateCsrfProtection($request, $configuration);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxonsPositions = $request->request->all('productTaxons');

        if (!$this->shouldProductsPositionsBeUpdated($request, $productTaxonsPositions)) {
            return $this->redirectHandler->redirectToReferer($configuration);
        }

        try {
            $productTaxonsPositionsMap = $this->mapFromArrayToProductTaxonPositionMap($productTaxonsPositions);
        } catch (\InvalidArgumentException $exception) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('error', $exception->getMessage());

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        /** @var PositionerInterface $positioner */
        $positioner = $this->get(PositionerInterface::class);

        foreach ($productTaxonsPositionsMap as $productTaxonPositionMap) {
            $positioner->updatePosition(
                $productTaxonPositionMap['productTaxon'],
                $productTaxonPositionMap['newPosition'],
                $this->getMaxPosition(),
            );
            $this->manager->flush();
        }

        return $this->redirectHandler->redirectToReferer($configuration);
    }

    /**
     * @param array<int, string> $productTaxonPositions
     *
     * @return array<array{productTaxon: ProductTaxonInterface, newPosition: int}>
     */
    private function mapFromArrayToProductTaxonPositionMap(array $productTaxonPositions): array
    {
        /** @var PositionerInterface $positioner */
        $positioner = $this->get(PositionerInterface::class);
        $map = [];

        foreach ($productTaxonPositions as $productTaxonId => $productTaxonPosition) {
            if (!is_numeric($productTaxonPosition)) {
                throw new \InvalidArgumentException(sprintf('The position "%s" is invalid.', $productTaxonPosition));
            }

            /** @var ProductTaxonInterface $productTaxon */
            $productTaxon = $this->repository->find($productTaxonId);
            $productTaxonPosition = (int) $productTaxonPosition;

            if (!$positioner->hasPositionChanged($productTaxon, $productTaxonPosition)) {
                continue;
            }

            $map[] = [
                'productTaxon' => $productTaxon,
                'newPosition' => $productTaxonPosition,
            ];
        }

        return $map;
    }

    private function getMaxPosition(): int
    {
        /** @var EntityRepository&RepositoryInterface $repository */
        $repository = $this->repository;

        return $repository->count([]) - 1;
    }

    private function validateCsrfProtection(Request $request, RequestConfiguration $configuration): void
    {
        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid('update-product-taxon-position', (string) $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }
    }

    private function shouldProductsPositionsBeUpdated(Request $request, ?array $productTaxons): bool
    {
        return in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null !== $productTaxons;
    }

    private function updatePositions(int $position, int $id): void
    {
        /** @var ProductTaxonInterface $productTaxonFromBase */
        $productTaxonFromBase = $this->repository->findOneBy(['id' => $id]);
        $productTaxonFromBase->setPosition($position);

        $this->manager->flush();
    }

    /**
     * @return mixed
     *
     * @deprecated This function will be removed in Sylius 2.0, since Symfony 5.4, use explicit input sources instead
     * based on Symfony\Component\HttpFoundation\Request::get
     */
    private function getParameterFromRequest(Request $request, string $key)
    {
        if ($request !== $result = $request->attributes->get($key, $request)) {
            return $result;
        }

        if ($request->query->has($key)) {
            return $request->query->all()[$key];
        }

        if ($request->request->has($key)) {
            return $request->request->all()[$key];
        }

        return null;
    }
}
