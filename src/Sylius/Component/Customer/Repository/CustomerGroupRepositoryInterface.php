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

namespace Sylius\Component\Customer\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

interface CustomerGroupRepositoryInterface extends RepositoryInterface
{
    public function findByPhrase(string $phrase, ?int $limit = null): iterable;
}
