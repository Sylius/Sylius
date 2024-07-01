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
     */
    public function updatePositionsAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxons = $this->getParameterFromRequest($request, 'productTaxons');
        $this->validateCsrfProtection($request, $configuration);

        if ($this->shouldProductsPositionsBeUpdated($request, $productTaxons)) {
            /** @var array{position: string|int, id: int} $productTaxon */
            foreach ($productTaxons as $productTaxon) {
                try {
                    $id = $productTaxon['id'];
                    $position = $productTaxon['position'];

                    /** @var ProductTaxonInterface $productTaxonFromBase */
                    $productTaxonFromBase = $this->repository->findOneBy(['id' => $id]);
                    $productTaxonFromBase->setPosition((int) $position);
                } catch (\InvalidArgumentException $exception) {
                    throw new HttpException(Response::HTTP_BAD_REQUEST, $exception->getMessage());
                }

                $this->manager->flush();
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

        $maxPosition = $this->getMaxPosition($productTaxonsPositions);

        try {
            $this->updatePositions($productTaxonsPositions, $maxPosition);
        } catch (\InvalidArgumentException $exception) {
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('error', $exception->getMessage());

            return $this->redirectHandler->redirectToReferer($configuration);
        }

        return $this->redirectHandler->redirectToReferer($configuration);
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

    /** @param array<int, string> $productTaxonsPositions */
    private function getMaxPosition(array $productTaxonsPositions): int
    {
        $firstProductTaxonId = array_keys($productTaxonsPositions)[0];

        /** @var EntityRepository&RepositoryInterface $repository */
        $repository = $this->repository;

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $repository->find($firstProductTaxonId);

        return $repository->count(['taxon' => $productTaxon->getTaxon()]) - 1;
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

    private function getPositioner(): PositionerInterface
    {
        /** @var PositionerInterface $positioner */
        $positioner = $this->get(PositionerInterface::class);

        return $positioner;
    }
}
