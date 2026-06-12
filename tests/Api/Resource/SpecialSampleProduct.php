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

namespace Sylius\Tests\Api\Resource;

/**
 * Simulates an entity subclass (e.g. a Doctrine discriminator map subclass)
 * that is not an API resource itself, but extends a registered API resource.
 */
class SpecialSampleProduct extends SampleProduct
{
}
