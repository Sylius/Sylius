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

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webmozart\Assert\Assert;

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

                $this->manager->flush();
            }
        }

        return new JsonResponse();
    }

    /**
     * @psalm-suppress DeprecatedMethod
     */
    public function updateProductTaxonsPositionsAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxons = $this->getParameterFromRequest($request, 'productTaxons');

        $this->validateCsrfProtection($request, $configuration);

        if ($this->shouldProductsPositionsBeUpdated($request, $productTaxons)) {
            /** @var Session $session */
            $session = $request->getSession();

            /** @var ProductTaxonInterface $productTaxon */
            foreach ($productTaxons as $id => $position) {
                try {
                    $this->updatePositions($position, $id);
                } catch (\InvalidArgumentException $exception) {
                    $session->getFlashBag()->add('error', $exception->getMessage());

                    return $this->redirectHandler->redirectToReferer($configuration);
                }
            }

            $this->manager->flush();
        }

        return $this->redirectHandler->redirectToReferer($configuration);
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

    private function updatePositions(string $position, int $id): void
    {
        Assert::numeric($position, sprintf('The position "%s" is invalid.', $position));

        /** @var ProductTaxonInterface $productTaxonFromBase */
        $productTaxonFromBase = $this->repository->findOneBy(['id' => $id]);
        $productTaxonFromBase->setPosition((int) $position);
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
