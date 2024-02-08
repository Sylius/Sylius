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

use Sylius\Bundle\ShopBundle\Locale\LocaleSwitcherInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;

final class LocaleSwitchController
{
    public function __construct(
        private Environment $templatingEngine,
        private LocaleContextInterface $localeContext,
        private LocaleProviderInterface $localeProvider,
        private LocaleSwitcherInterface $localeSwitcher,
    ) {
    }

    public function renderAction(): Response
    {
        return new Response($this->templatingEngine->render('@SyliusShop/Menu/_localeSwitch.html.twig', [
            'active' => $this->localeContext->getLocaleCode(),
            'locales' => $this->localeProvider->getAvailableLocalesCodes(),
        ]));
    }

    public function switchAction(Request $request, ?string $code = null): Response
    {
        if (null === $code) {
            $code = $this->localeProvider->getDefaultLocaleCode();
        }

        if (!in_array($code, $this->localeProvider->getAvailableLocalesCodes(), true)) {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                sprintf('The locale code "%s" is invalid.', $code),
            );
        }

        return $this->localeSwitcher->handle($request, $code);
    }
}
