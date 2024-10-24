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

namespace Sylius\Bundle\CoreBundle\Collector;

use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class SyliusCollector extends DataCollector
{
    public function __construct(
        private ShopperContextInterface $shopperContext,
        array $bundles,
        string $defaultLocaleCode,
    ) {
        $this->data = [
            'version' => SyliusCoreBundle::VERSION,
            'base_currency_code' => null,
            'currency_code' => null,
            'default_locale_code' => $defaultLocaleCode,
            'locale_code' => null,
            'extensions' => [
                'SyliusApiBundle' => ['name' => 'API', 'enabled' => false],
                'SyliusAdminBundle' => ['name' => 'Admin', 'enabled' => false],
                'SyliusShopBundle' => ['name' => 'Shop', 'enabled' => false],
            ],
        ];

        foreach (array_keys($this->data['extensions']) as $bundleName) {
            if (isset($bundles[$bundleName])) {
                $this->data['extensions'][$bundleName]['enabled'] = true;
            }
        }
    }

    public function getVersion(): string
    {
        return $this->data['version'];
    }

    public function getExtensions(): array
    {
        return $this->data['extensions'];
    }

    public function getCurrencyCode(): ?string
    {
        return $this->data['currency_code'];
    }

    public function getLocaleCode(): ?string
    {
        return $this->data['locale_code'];
    }

    public function getDefaultCurrencyCode(): ?string
    {
        return $this->data['base_currency_code'];
    }

    public function getDefaultLocaleCode(): ?string
    {
        return $this->data['default_locale_code'];
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->shopperContext->getChannel();

            $this->data['base_currency_code'] = $channel->getBaseCurrency()->getCode();
            $this->data['currency_code'] = $this->shopperContext->getCurrencyCode();
        } catch (ChannelNotFoundException | CurrencyNotFoundException) {
        }

        try {
            $this->data['locale_code'] = $this->shopperContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
        }
    }

    public function reset(): void
    {
        $this->data['base_currency_code'] = null;
        $this->data['currency_code'] = null;
        $this->data['locale_code'] = null;
    }

    public function getName(): string
    {
        return 'sylius_core';
    }
}
