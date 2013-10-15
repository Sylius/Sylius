<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;

/**
 * OrderConfirmationMailer implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class OrderConfirmationMailer implements OrderConfirmationMailerInterface
{
    /**
     * @var TwigMailerInterface
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $parameters;

    public function __construct(TwigMailerInterface $mailer, array $parameters)
    {
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function sendOrderConfirmation(OrderInterface $order)
    {
        if (!$user = $order->getUser()) {
            throw new \InvalidArgumentException('Order has to belong to a User');
        }

        $template = $this->parameters['template'];
        $from = $this->parameters['from_email'];
        $to = $order->getUser()->getEmail();
        $context = array(
            'order' => $order
        );

        $this->mailer->sendEmail($template, $context, $from, $to);
    }
}