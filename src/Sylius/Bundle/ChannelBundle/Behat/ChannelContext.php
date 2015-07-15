<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

class ChannelContext extends DefaultContext
{
    /**
     * @Given /^all products assigned to "([^""]*)" channel$/
     */
    public function assignChannelToProducts($code)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));
        /** @var ProductInterface[] $products */
        $products = $this->getRepository('product')->findAll();
        foreach ($products as $product) {
            $product->addChannel($channel);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^channel "([^""]*)" has following products assigned:$/
     */
    public function channelHasFollowingProductsAssigned($code, TableNode $table)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));

        foreach ($table->getHash() as $product) {
            /** @var ProductInterface $product */
            $product = $this->getRepository('product')->findOneBy(array('name' => $product));

            $product->addChannel($channel);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^all promotions assigned to "([^""]*)" channel$/
     */
    public function assignChannelToPromotions($code)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));
        /** @var ChannelsAwareInterface[] $promotions */
        $promotions = $this->getRepository('promotion')->findAll();
        foreach ($promotions as $promotion) {
            $promotion->addChannel($channel);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there is default channel configured$/
     */
    public function setupDefaultChannel()
    {
        $this->thereIsChannel('DEFAULT-WEB', "Default", "localhost", "en_US", "EUR");
    }

    /**
     * @Given /^there are following channels configured:$/
     */
    public function thereAreFollowingChannels(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsChannel(
                $data['code'],
                $data['name'],
                isset($data['url']) ? $data['url'] : null,
                isset($data['locales']) ? $data['locales'] : null,
                isset($data['currencies']) ? $data['currencies'] : null,
                isset($data['shipping']) ? $data['shipping'] : null,
                isset($data['payments']) ? $data['payments'] : null,
                false
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^channel "([^""]*)" has following configuration:$/
     */
    public function channelHasFollowingConfiguration($code, TableNode $table)
    {
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));

        foreach ($table->getHash() as $data) {
            $this->configureChannel(
                $channel,
                isset($data['locales']) ? $data['locales'] : null,
                isset($data['currencies']) ? $data['currencies'] : null,
                isset($data['shipping']) ? $data['shipping'] : null,
                isset($data['payment']) ? $data['payment'] : null,
                isset($data['taxonomy']) ? $data['taxonomy'] : null
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^There is channel "([^""]*)" named "([^""]*)" for url "([^""]*)"$/
     */
    public function thereIsChannel($code, $name, $url, $locales = null, $currencies = "EUR", $shippingMethods = null, $paymentMethos = null, $flush = true)
    {
        /* @var $channel ChannelInterface */
        $channel = $this->getRepository('channel')->createNew();
        $channel->setCode($code);
        $channel->setName($name);
        $channel->setUrl($url);

        $this->configureChannel($channel, $locales, $currencies, $shippingMethods, $paymentMethos);

        $manager = $this->getEntityManager();
        $manager->persist($channel);
        if ($flush) {
            $manager->flush();
        }

        return $channel;
    }

    private function configureChannel(ChannelInterface $channel, $localeCodes = null, $currencyCodes = null, $shippingMethodNames = null, $paymentMethodNames = null, $taxonomyNames = null)
    {
        if ($shippingMethodNames) {
            $shippingMethodNames = array_map('trim', explode(',', $shippingMethodNames));
            foreach ($shippingMethodNames as $shippingMethodName) {
                $shippingMethod = $shippingMethods = $this->getRepository('shipping_method')->findOneBy(array('name' => $shippingMethodName));
                $channel->addShippingMethod($shippingMethod);
            }
        }

        if ($paymentMethodNames) {
            $paymentMethodNames = array_map('trim', explode(',', $paymentMethodNames));
            $paymentMethods = $this->getRepository('payment_method')->findBy(array('name' => $paymentMethodNames));
            foreach ($paymentMethods as $paymentMethod) {
                $channel->addPaymentMethod($paymentMethod);
            }
        }

        if ($localeCodes) {
            $localeCodes = array_map('trim', explode(',', $localeCodes));
            $locales = $this->getRepository('locale')->findBy(array('code' => $localeCodes));
            foreach ($locales as $locale) {
                $channel->addLocale($locale);
            }
        }

        if ($currencyCodes) {
            $currencyCodes = array_map('trim', explode(',', $currencyCodes));
            $currencies = $this->getRepository('currency')->findBy(array('code' => $currencyCodes));
            foreach ($currencies as $currency) {
                $channel->addCurrency($currency);
            }
        }

        if ($taxonomyNames) {
            $taxonomyNames = array_map('trim', explode(',', $taxonomyNames));

            foreach ($taxonomyNames as $taxonomyName) {
                $taxonomy = $this->getRepository('taxonomy')->findOneBy(array('name' => $taxonomyName));
                $channel->addTaxonomy($taxonomy);
            }
        }
    }
}
