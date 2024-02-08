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
