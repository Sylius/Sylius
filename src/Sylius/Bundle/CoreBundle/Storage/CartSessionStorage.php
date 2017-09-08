<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Storage;

use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorage implements CartStorageInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @param SessionInterface $session
     * @param string $sessionKeyName
     */
    public function __construct(SessionInterface $session, string $sessionKeyName)
    {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCartId(string $channelCode): bool
    {
        return $this->session->has($this->getCartKeyName($channelCode));
    }

    /**
     * {@inheritdoc}
     */
    public function getCartId(string $channelCode): ?int
    {
        return $this->session->get($this->getCartKeyName($channelCode));
    }

    /**
     * {@inheritdoc}
     */
    public function setCartId(string $channelCode, $cartId): void
    {
        $this->session->set($this->getCartKeyName($channelCode), $cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCartId(string $channelCode): void
    {
        $this->session->remove($this->getCartKeyName($channelCode));
    }

    /**
     * @param string $channelCode
     *
     * @return string
     */
    private function getCartKeyName(string $channelCode): string
    {
        return sprintf('%s.%s', $this->sessionKeyName, $channelCode);
    }
}
