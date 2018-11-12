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

namespace spec\Sylius\Component\Resource\Fixtures;

use Sylius\Component\Resource\Model\ResourceInterface;

interface SampleBookResourceInterface extends ResourceInterface
{
    public function getName(): string;

    public function getRating(): int;

    public function getTitle(): string;
}
