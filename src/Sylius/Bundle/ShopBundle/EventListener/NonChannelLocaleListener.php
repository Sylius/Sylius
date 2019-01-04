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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

final class NonChannelLocaleListener
{
    /** @var LocaleProviderInterface */
    private $channelBasedLocaleProvider;

    /** @var FirewallMap */
    private $firewallMap;

    /** @var string[] */
    private $firewallNames;

    /**
     * @param string[] $firewallNames
     */
    public function __construct(
        LocaleProviderInterface $channelBasedLocaleProvider,
        FirewallMap $firewallMap,
        array $firewallNames
    ) {
        Assert::notEmpty($firewallNames);
        Assert::allString($firewallNames);

        $this->channelBasedLocaleProvider = $channelBasedLocaleProvider;
        $this->firewallMap = $firewallMap;
        $this->firewallNames = $firewallNames;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function restrictRequestLocale(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes && in_array($request->attributes->get('_route'), ['_wdt', '_profiler'])) {
            return;
        }

        $currentFirewall = $this->firewallMap->getFirewallConfig($request);
        if (!$this->isFirewallSupported($currentFirewall)) {
            return;
        }

        $requestLocale = $request->getLocale();
        if (!in_array($requestLocale, $this->channelBasedLocaleProvider->getAvailableLocalesCodes(), true)) {
            throw new NotFoundHttpException(
                sprintf('The "%s" locale is unavailable in this channel.', $requestLocale)
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
