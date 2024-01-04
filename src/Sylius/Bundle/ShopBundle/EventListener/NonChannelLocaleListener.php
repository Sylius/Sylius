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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class NonChannelLocaleListener
{
    /** @var string[] */
    private $firewallNames;

    /**
     * @param string[] $firewallNames
     */
    public function __construct(
        private RouterInterface $router,
        private LocaleProviderInterface $channelBasedLocaleProvider,
        private FirewallMap $firewallMap,
        array $firewallNames,
    ) {
        Assert::notEmpty($firewallNames);
        Assert::allString($firewallNames);
        $this->firewallNames = $firewallNames;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function restrictRequestLocale(RequestEvent $event): void
    {
        if (\method_exists($event, 'isMainRequest')) {
            $isMainRequest = $event->isMainRequest();
        } else {
            /** @phpstan-ignore-next-line */
            $isMainRequest = $event->isMasterRequest();
        }
        if (!$isMainRequest) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->attributes->has('_locale')) {
            return;
        }

        $currentFirewall = $this->firewallMap->getFirewallConfig($request);
        if (!$this->isFirewallSupported($currentFirewall)) {
            return;
        }

        $requestLocale = $request->getLocale();
        if (!in_array($requestLocale, $this->channelBasedLocaleProvider->getAvailableLocalesCodes(), true)) {
            $event->setResponse(
                new RedirectResponse(
                    $this->router->generate(
                        'sylius_shop_homepage',
                        ['_locale' => $this->channelBasedLocaleProvider->getDefaultLocaleCode()],
                    ),
                ),
            );
        }
    }

    private function isFirewallSupported(?FirewallConfig $firewall = null): bool
    {
        return
            null !== $firewall &&
            in_array($firewall->getName(), $this->firewallNames)
        ;
    }
}
