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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntity;

final class HasEnabledEntityTest extends TestCase
{
    private HasEnabledEntity $hasEnabledEntity;

    protected function setUp(): void
    {
        $this->hasEnabledEntity = new HasEnabledEntity();
    }

    public function testHasValidator(): void
    {
        $this->assertSame('sylius_has_enabled_entity', $this->hasEnabledEntity->validatedBy());
    }

    public function testHasATarget(): void
    {
        $this->assertSame('class', $this->hasEnabledEntity->getTargets());
    }
}
