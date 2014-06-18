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
        $repository = $this->getRepository('payment_method');

        foreach ($table->getHash() as $data) {
            /* @var $method PaymentMethodInterface */
            $method = $repository->createNew();
            $method->setName(trim($data['name']));
            $method->setGateway(trim($data['gateway']));

            $enabled = true;

            if (isset($data['enabled'])) {
                $enabled = 'yes' === trim($data['enabled']);
            }

            $method->setEnabled($enabled);

            $manager->persist($method);
        }

        $manager->flush();
    }
}
