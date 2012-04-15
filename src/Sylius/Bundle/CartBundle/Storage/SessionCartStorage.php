<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Storage;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Stores current cart id inside the user session.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SessionCartStorage implements CartStorageInterface
{
    /**
     * Session.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCartIdentifier()
    {
        return $this->session->get('_sylius.cart-id');
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCartIdentifier(CartInterface $cart)
    {
        $this->session->set('_sylius.cart-id', $cart->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function resetCurrentCartIdentifier()
    {
        $this->session->remove('_sylius.cart-id');
    }
}
