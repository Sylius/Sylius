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

namespace Sylius\Bundle\CoreBundle\Collector;

use Sylius\Bundle\CoreBundle\Application\Kernel;
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
    /** @var ShopperContextInterface */
    private $shopperContext;

    public function __construct(
        ShopperContextInterface $shopperContext,
        array $bundles,
        string $defaultLocaleCode
    ) {
        $this->shopperContext = $shopperContext;

        $this->data = [
            'version' => Kernel::VERSION,
            'base_currency_code' => null,
            'currency_code' => null,
            'default_locale_code' => $defaultLocaleCode,
            'locale_code' => null,
            'extensions' => [
                'SyliusAdminApiBundle' => ['name' => 'API', 'enabled' => false],
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

    /**
     * @return string
     */
    public function getCurrencyCode(): ?string
    {
        return $this->data['currency_code'];
    }

    /**
     * @return string
     */
    public function getLocaleCode(): ?string
    {
        return $this->data['locale_code'];
    }

    /**
     * @return string
     */
    public function getDefaultCurrencyCode(): ?string
    {
        return $this->data['base_currency_code'];
    }

    /**
     * @return string
     */
    public function getDefaultLocaleCode(): ?string
    {
        return $this->data['default_locale_code'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->shopperContext->getChannel();

            $this->data['base_currency_code'] = $channel->getBaseCurrency()->getCode();
            $this->data['currency_code'] = $this->shopperContext->getCurrencyCode();
        } catch (ChannelNotFoundException | CurrencyNotFoundException $exception) {
        }

        try {
            $this->data['locale_code'] = $this->shopperContext->getLocaleCode();
        } catch (LocaleNotFoundException $exception) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data['base_currency_code'] = null;
        $this->data['currency_code'] = null;
        $this->data['locale_code'] = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_core';
    }
}
