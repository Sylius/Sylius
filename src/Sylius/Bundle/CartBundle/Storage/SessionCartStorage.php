<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartsBundle\Storage;

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
    public function getCurrentCartId()
    {
        return $this->session->get('_sylius.cart-id');
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCartId($identifier)
    {
        $this->session->set('_sylius.cart-id', $identifier);
    }
}
