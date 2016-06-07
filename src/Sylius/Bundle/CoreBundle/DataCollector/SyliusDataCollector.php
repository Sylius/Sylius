<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataCollector;

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SyliusDataCollector extends DataCollector
{
    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $channelCode;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $localeCode;

    /**
     * @var string
     */
    private $customerEmail;

    /**
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(ShopperContextInterface $shopperContext)
    {
        $this->shopperContext = $shopperContext;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getChannelCode()
    {
        return $this->channelCode;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->version = Kernel::VERSION;

        $this->channelCode = $this->shopperContext->getChannel()->getCode();
        $this->currencyCode = $this->shopperContext->getCurrencyCode();
        $this->localeCode = $this->shopperContext->getLocaleCode();

        $customer = $this->shopperContext->getCustomer();

        if (null !== $customer) {
            $this->customerEmail = $customer->getEmail();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->version, $this->channelCode, $this->currencyCode, $this->localeCode, $this->customerEmail]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->version, $this->channelCode, $this->currencyCode, $this->localeCode, $this->customerEmail) = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_core';
    }
}
