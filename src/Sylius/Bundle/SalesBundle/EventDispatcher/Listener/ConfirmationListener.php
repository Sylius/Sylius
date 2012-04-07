<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\EventDispatcher\Listener;

use Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Confirmation listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ConfirmationListener
{
    /**
     * Options.
     *
     * @var array
     */
    private $options;

    /**
     * Templating engine.
     *
     * @var EngineInterface
     */
    private $templating;

    /**
     * Mailer service.
     */
    private $mailer;

    public function __construct(array $options, $mailer, EngineInterface $templating)
    {
        $this->options = $options;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Catches placed order.
     * Sets confirmed to false and sends confirmation email to buyer.
     *
     * @param FilterOrderEvent $event
     */
    public function onOrderPlace(FilterOrderEvent $event)
    {
        $order = $event->getOrder();
        $order->setConfirmed(false);

        $message = \Swift_Message::newInstance()
        ->setSubject($this->options['email']['subject'])
        ->setFrom($this->options['email']['from'])
        ->setTo($user->getEmail())
        ->setBody($this->templating->render($this->options['email']['template'], array(
            'order' => $order
        )));

        $this->mailer->send($message);
    }
}
