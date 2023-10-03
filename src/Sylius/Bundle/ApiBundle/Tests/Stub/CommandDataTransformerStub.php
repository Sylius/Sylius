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

namespace Sylius\Bundle\ApiBundle\Tests\Stub;

use Sylius\Bundle\ApiBundle\Attribute\AsCommandDataTransformer;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;

#[AsCommandDataTransformer(priority: 15)]
final class CommandDataTransformerStub implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        return new \stdClass();
    }

    public function supportsTransformation($object): bool
    {
        return true;
    }
}
