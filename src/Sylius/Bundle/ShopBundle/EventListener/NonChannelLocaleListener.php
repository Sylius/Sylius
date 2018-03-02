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
    /**
     * @var LocaleProviderInterface
     */
    private $channelBasedLocaleProvider;

    /**
     * @var FirewallMap
     */
    private $firewallMap;

    /**
     * @var string[]
     */
    private $firewallNames;

    /**
     * @param LocaleProviderInterface $channelBasedLocaleProvider
     * @param FirewallMap $firewallMap
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
     * @param GetResponseEvent $event
     *
     * @throws NotFoundHttpException
     */
    public function restrictRequestLocale(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentFirewall = $this->firewallMap->getFirewallConfig($request);
        if (!$this->isFirewallSupported($currentFirewall)) {
            return;
        }

        $requestLocale = $request->getLocale();
        $availableLocalesCodes = $this->channelBasedLocaleProvider->getAvailableLocalesCodes();

        //vd_fix: Ignore if is default installation locale code
        if (!in_array($requestLocale,$availableLocalesCodes, true) && $requestLocale != "en_US" ) {
            throw new NotFoundHttpException(
                sprintf('The "%s" locale is unavailable in this channel.', $requestLocale)
            );
        }
    }

    /**
     * @param FirewallConfig|null $firewall
     *
     * @return bool
     */
    private function isFirewallSupported(?FirewallConfig $firewall = null): bool
    {
        return
            null !== $firewall &&
            in_array($firewall->getName(), $this->firewallNames)
        ;
    }
}
