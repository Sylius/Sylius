<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ProductTaxonController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updatePositionsAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxons = $request->get('productTaxons');

        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid('update-product-taxon-position', $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null !== $productTaxons) {
            /** @var ProductTaxonInterface $productTaxon */
            foreach ($productTaxons as $productTaxon) {
                if (!is_numeric($productTaxon['position'])) {
                    throw new HttpException(
                        Response::HTTP_BAD_REQUEST,
                        sprintf('The productTaxon position "%s" is invalid.', $productTaxon['position'])
                    );
                }

                $productTaxonFromBase = $this->repository->findOneBy(['id' => $productTaxon['id']]);
                $productTaxonFromBase->setPosition($productTaxon['position']);

                $this->manager->flush();
            }
        }

        return new JsonResponse();
    }
}
