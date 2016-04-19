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
        $manager->persist($this->createChannel('DEFAULT', 'Default', $url, ['en_US'], ['USD'], ['category', 'brand'], ['fedex', 'fedex_world'], ['offline']));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 40;
    }

    /**
     * @param string $code
     * @param string $name
     * @param string $url
     * @param array  $locales
     * @param array  $currencies
     * @param array  $taxons
     * @param array  $shippingMethods
     * @param array  $paymentMethods
     *
     * @return ChannelInterface
     */
    protected function createChannel($code, $name, $url, array $locales = [], array $currencies = [], array $taxons = [], array $shippingMethods = [], array $paymentMethods = [])
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getChannelFactory()->createNew();
        $channel->setHostname($url);
        $channel->setCode($code);
        $channel->setName($name);
        $channel->setColor($this->faker->randomElement(['Red', 'Green', 'Blue', 'Orange', 'Pink']));

        $this->setReference('Sylius.Channel.'.$code, $channel);

        foreach ($locales as $locale) {
            $channel->addLocale($this->getReference('Sylius.Locale.'.$locale));
        }
        foreach ($currencies as $currency) {
            $channel->addCurrency($this->getReference('Sylius.Currency.'.$currency));
        }
        foreach ($taxons as $taxon) {
            $channel->addTaxon($this->getReference('Sylius.Taxon.'.$taxon));
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
