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
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * Sample payment methods.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadPaymentMethodsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createPaymentMethod('offline', 'Offline', 'offline'));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * @param string  $code
     * @param string  $name
     * @param string  $gateway
     * @param bool $enabled
     *
     * @return PaymentMethodInterface
     */
    protected function createPaymentMethod($code, $name, $gateway, $enabled = true)
    {
        /* @var $method PaymentMethodInterface */
        $method = $this->getPaymentMethodFactory()->createNew();

        $translatedNames = [
            $this->defaultLocale => sprintf($name),
            'es_ES' => sprintf($this->fakers['es_ES']->word),
        ];
        $this->addTranslatedFields($method, $translatedNames);

        $method->setGateway($gateway);
        $method->setEnabled($enabled);
        $method->setCode($code);

        $this->setReference('Sylius.PaymentMethod.'.$code, $method);

        return $method;
    }

    private function addTranslatedFields(PaymentMethodInterface $method, $translatedNames)
    {
        foreach ($translatedNames as $locale => $name) {
            $method->setCurrentLocale($locale);
            $method->setFallbackLocale($locale);

            $method->setName($name);
            $method->setDescription($this->fakers[$locale]->paragraph);
        }

        $method->setCurrentLocale($this->defaultLocale);
    }
}
