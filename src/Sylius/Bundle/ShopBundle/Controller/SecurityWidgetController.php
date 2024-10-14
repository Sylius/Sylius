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

namespace Sylius\Bundle\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

trigger_deprecation(
    'sylius/shop-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    SecurityWidgetController::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class SecurityWidgetController
{
    public function __construct(private Environment $templatingEngine)
    {
    }

    public function renderAction(): Response
    {
        return new Response($this->templatingEngine->render('@SyliusShop/Menu/_security.html.twig'));
    }
}
