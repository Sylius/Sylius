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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionArchivalProcessorInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @experimental */
final class RestoreCatalogPromotionAction
{
    public function __construct(
        private CatalogPromotionArchivalProcessorInterface $catalogPromotionArchivalProcessor,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->catalogPromotionArchivalProcessor->restoreCatalogPromotion($request->attributes->get('code'));

            return new Response(status: Response::HTTP_ACCEPTED);
        } catch (CatalogPromotionNotFoundException) {
            return new JsonResponse(status: Response::HTTP_NOT_FOUND);
        } catch (InvalidCatalogPromotionStateException $exception) {
            return new JsonResponse(
                ['code' => Response::HTTP_BAD_REQUEST, 'message' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
