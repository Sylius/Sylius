<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * Channel fixtures.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadChannelData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $url = $this->container->getParameter('router.request_context.host');
        $manager->persist($this->createChannel('WEB-UK', 'UK Webstore', $url, array('en_GB'), array('GBP'), array('DHL', 'UPS Ground'), array('Dummy', 'StripeCheckout')));
        $manager->persist($this->createChannel('WEB-DE', 'Germany Webstore', null, array('de_DE'), array('EUR'), array('DHL', 'UPS Ground'), array('Dummy', 'StripeCheckout')));
        $manager->persist($this->createChannel('WEB-US', 'United States Webstore', null, array('en_US'), array('USD'), array('FedEx', 'FedEx World Shipping'), array('Dummy', 'StripeCheckout')));
        $manager->persist($this->createChannel('MOBILE', 'Mobile Store', null, array('en_GB', 'de_DE'), array('GBP', 'USD', 'EUR'), array('DHL', 'UPS Ground', 'FedEx'), array('Dummy', 'StripeCheckout')));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 5;
    }

    /**
     * @param string $name
     * @param array  $locales
     * @param array  $currencies
     * @param array  $shippingMethods
     * @param array  $paymentMethods
     *
     * @return ChannelInterface
     */
    protected function createChannel($code, $name, $url, array $locales = array(), array $currencies = array(), array $shippingMethods = array(), array $paymentMethods = array())
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getChannelRepository()->createNew();
        $channel->setUrl($url);
        $channel->setCode($code);
        $channel->setName($name);
        $channel->setColor($this->faker->randomElement(array('Red', 'Green', 'Blue', 'Orange', 'Pink')));

        $this->setReference('Sylius.Channel.'.$code, $channel);

        foreach ($locales as $locale) {
            $channel->addLocale($this->getReference('Sylius.Locale.'.$locale));
        }
        foreach ($currencies as $currency) {
            $channel->addCurrency($this->getReference('Sylius.Currency.'.$currency));
        }
        foreach ($shippingMethods as $shippingMethod) {
            $channel->addShippingMethod($this->getReference('Sylius.ShippingMethod.'.$shippingMethod));
        }
        foreach ($paymentMethods as $paymentMethod) {
            $channel->addPaymentMethod($this->getReference('Sylius.PaymentMethod.'.$paymentMethod));
        }

        return $channel;
    }
}
