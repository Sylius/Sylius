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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductTaxonController extends ResourceController
{
    /**
     * @throws HttpException
     */
    public function updatePositionsAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productTaxons = $request->get('productTaxons');

        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid('update-product-taxon-position', $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null !== $productTaxons) {
            /** @var Session $session */
            $session = $request->getSession();

            /** @var ProductTaxonInterface $productTaxon */
            foreach ($productTaxons as $id => $position) {
                if (!is_numeric($position)) {
                    $session->getFlashBag()->add('error', sprintf('The position "%s" is invalid.', $position));

                    return $this->redirectHandler->redirectToReferer($configuration);
                }

                /** @var ProductTaxonInterface $productTaxonFromBase */
                $productTaxonFromBase = $this->repository->findOneBy(['id' => $id]);
                $productTaxonFromBase->setPosition((int) $position);
            }

            $this->manager->flush();
        }

        return $this->redirectHandler->redirectToReferer($configuration);
    }
}
