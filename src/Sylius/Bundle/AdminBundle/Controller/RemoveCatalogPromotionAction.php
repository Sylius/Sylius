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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RemoveCatalogPromotionAction
{
    public function __construct(private CatalogPromotionRemovalProcessorInterface $catalogPromotionRemovalProcessor)
    {
    }

    public function __invoke(Request $request): Response
    {
        $catalogPromotionCode = $request->attributes->get('code');
        if (null === $catalogPromotionCode) {
            throw new NotFoundHttpException('The catalog promotion has not been found');
        }

        try {
            $this->catalogPromotionRemovalProcessor->removeCatalogPromotion($catalogPromotionCode);

            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'sylius.catalog_promotion.remove');

            return new RedirectResponse($request->headers->get('referer'));
        } catch (CatalogPromotionNotFoundException) {
            throw new NotFoundHttpException('The catalog promotion has not been found');
        } catch (InvalidCatalogPromotionStateException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }
}
