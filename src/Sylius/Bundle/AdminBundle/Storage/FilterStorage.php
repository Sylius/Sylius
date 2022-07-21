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

namespace Sylius\Bundle\AdminBundle\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class FilterStorage implements FilterStorageInterface
{
    public function __construct(private SessionInterface $session)
    {
    }

    public function set(array $filters): void
    {
        $this->session->set('filters', $filters);
    }

    public function all(): array
    {
        return $this->session->get('filters', []);
    }

    public function hasFilters(): bool
    {
        return [] !== $this->session->get('filters', []);
    }
}
