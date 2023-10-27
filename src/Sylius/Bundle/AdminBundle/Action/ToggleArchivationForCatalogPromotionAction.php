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

namespace Sylius\Bundle\AdminBundle\Action;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionArchivalProcessorInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionAlreadyArchivedException;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\Assert\Assert;

final class ToggleArchivationForCatalogPromotionAction
{
    public function __construct(
        private CatalogPromotionArchivalProcessorInterface $catalogPromotionArchivalProcessor,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $code = $request->attributes->get('code');
        $csrfToken = $request->request->all("sylius_archivable")['_token'];

        $csrfValue = 'sylius_archivable';
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($csrfValue, (string) $csrfToken))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var Session $session */
        $session = $request->getSession();

        try {
            if ($this->catalogPromotionArchivalProcessor->canBeArchived($code)) {
                $this->catalogPromotionArchivalProcessor->archive($code);
                $session->getFlashBag()->add('success', 'sylius.catalog_promotion.archive');

                return new RedirectResponse($request->headers->get('referer'));
            } else {
                $this->catalogPromotionArchivalProcessor->restore($code);
                $session->getFlashBag()->add('success', 'sylius.catalog_promotion.restore');

                return new RedirectResponse($request->headers->get('referer'));
            }
        } catch (CatalogPromotionNotFoundException) {
            throw new NotFoundHttpException('The catalog promotion has not been found');
        } catch (InvalidCatalogPromotionStateException $exception) {
            throw new BadRequestException($exception->getMessage());
        }
    }
}
