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

final class FilterStorage implements FilterStorageInterface
{
    public function __construct(private RequestStack|SessionInterface $requestStackOrSession)
    {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }
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
        if ($this->requestStackOrSession instanceof RequestStack) {
            return $this->requestStackOrSession->getSession();
        }

        return $this->requestStackOrSession;
    }
}
