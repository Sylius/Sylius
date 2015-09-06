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
        foreach ($table->getHash() as $data) {
            $this->thereIsPaymentMethod(
                $data['name'],
                isset($data['gateway']) ? $data['gateway'] : null,
                isset($data['calculator']) ? $data['calculator'] : null,
                isset($data['calculator_configuration']) ? $data['calculator_configuration'] : null,
                isset($data['enabled']) ? $data['enabled'] : false,
                false
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^There is payment method "([^""]*)" with "([^""]*)" gateway$/
     * @Given /^there is an enabled payment method "([^""]*)"$/
     */
    public function thereIsPaymentMethod(
        $name,
        $gateway = 'stripe',
        $calculator = 'fixed',
        $calculatorConfig = 'amount: 0',
        $enabled = true,
        $flush = true
    ) {
        $repository = $this->getRepository('payment_method');
        /* @var $method PaymentMethodInterface */
        if (null === $method = $repository->findOneBy(array('name' => $name))) {
            $method = $repository->createNew();
            $method->setName($name);
            $method->setGateway($gateway);
            $method->setFeeCalculator($calculator);
            $method->setFeeCalculatorConfiguration($this->getConfiguration($calculatorConfig));
        }

        $method->setEnabled($enabled);

        $manager = $this->getEntityManager();
        $manager->persist($method);

        if ($flush) {
            $manager->flush();
        }

        return $method;
    }

    /**
     * @Given /^there is a disabled payment method "([^""]*)"$/
     */
    public function thereIsDisabledPaymentMethod($name)
    {
        $this->thereIsPaymentMethod($name, 'stripe', 'fixed', 'amount: 0', false);
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
