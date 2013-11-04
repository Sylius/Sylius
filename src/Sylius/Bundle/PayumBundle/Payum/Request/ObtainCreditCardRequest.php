<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Request;

use Sylius\Bundle\PaymentsBundle\Model\CreditCardInterface;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;

class ObtainCreditCardRequest
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var CreditCardInterface
     */
    protected $creditCard;

    /**
     * @param OrderInterface $order
     */
    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param CreditCardInterface $creditCard
     */
    public function setCreditCard(CreditCardInterface $creditCard)
    {
        $this->creditCard = $creditCard;
    }

    /**
     * @return CreditCardInterface
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }
}
