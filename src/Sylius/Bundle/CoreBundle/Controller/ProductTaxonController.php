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

use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updatePositionsAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxons = $request->get('productTaxons');

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null !== $productTaxons) {
            foreach($productTaxons as $productTaxon) {

                if(!is_numeric($productTaxon['position'])) {
                    throw new HttpException(
                        Response::HTTP_NOT_ACCEPTABLE,
                        sprintf('The productTaxon position "%s" is invalid.', $productTaxon['position'])
                    );
                }

                /** @var ProductTaxonInterface $productTaxon */
                $productTaxonFromBase = $this->repository->findOneBy(['id' => $productTaxon['id']]);
                $productTaxonFromBase->setPosition($productTaxon['position']);
                $this->manager->flush();
            }
        }
        return new JsonResponse();
    }
}
