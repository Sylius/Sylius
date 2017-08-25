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

namespace Sylius\Bundle\ResourceBundle\Storage;

use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class SessionStorage implements StorageInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return $this->session->has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->session->get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->session->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): void
    {
        $this->session->remove($name);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->session->all();
    }
}
