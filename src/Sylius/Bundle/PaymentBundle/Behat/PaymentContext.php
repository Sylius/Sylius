<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

class PaymentContext extends DefaultContext
{
    /**
     * @Given /^there are payment methods:$/
     * @Given /^there are following payment methods:$/
     * @Given /^the following payment methods exist:$/
     */
    public function thereArePaymentMethods(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('payment_method');

        foreach ($table->getHash() as $data) {
            if (!isset($data['calculator'], $data['calculator_configuration'])) {
                $data['calculator'] = 'fixed';
                $data['calculator_configuration'] = 'amount: 0';
            }

            /* @var $method PaymentMethodInterface */
            $method = $factory->createNew();
            $method->setCode(trim($data['code']));
            $method->setName(trim($data['name']));
            $method->setGateway(trim($data['gateway']));

            $method->setEnabled(isset($data['enabled']) ? 'yes' === trim($data['enabled']) : true);

            $manager->persist($method);
        }

        $manager->flush();
    }

    /**
     * @Given the payment method translations exist:
     */
    public function thePaymentMethodTranslationsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $paymentMethodTranslation = $this->findOneByName('payment_method_translation', $data['payment method']);

            $paymentMethod = $paymentMethodTranslation->getTranslatable();
            $paymentMethod->setCurrentLocale($data['locale']);
            $paymentMethod->setFallbackLocale($data['locale']);

            $paymentMethod->setName($data['name']);
        }

        $manager->flush();
    }
}
