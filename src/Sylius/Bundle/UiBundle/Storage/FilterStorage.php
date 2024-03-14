<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Storage;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final readonly class FilterStorage implements FilterStorageInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function set(array $filters): void
    {
        $this->getSession()->set('filters', $filters);
    }

    public function all(): array
    {
        return $this->getSession()->get('filters', []);
    }

    public function hasFilters(): bool
    {
        return [] !== $this->getSession()->get('filters', []);
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
