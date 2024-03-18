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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RemoveCatalogPromotionAction
{
    public function __construct(
        private CatalogPromotionRemovalProcessorInterface $catalogPromotionRemovalProcessor,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $catalogPromotionCode = $request->attributes->get('code');
        if (null === $catalogPromotionCode) {
            return new JsonResponse(status: Response::HTTP_NOT_FOUND);
        }

        try {
            $this->catalogPromotionRemovalProcessor->removeCatalogPromotion($catalogPromotionCode);

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
