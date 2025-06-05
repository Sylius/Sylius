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

namespace Tests\Sylius\Bundle\CoreBundle\Installer\Requirement;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Installer\Requirement\Requirement;

final class RequirementTest extends TestCase
{
    private Requirement $requirement;

    protected function setUp(): void
    {
        $this->requirement = new Requirement('PHP version', true, true, 'Please upgrade.');
    }

    public function testGetsLabel(): void
    {
        $this->assertSame('PHP version', $this->requirement->getLabel());
    }

    public function testGetsFulfilled(): void
    {
        $this->assertTrue($this->requirement->isFulfilled());
    }

    public function testGetsRequired(): void
    {
        $this->assertTrue($this->requirement->isRequired());
    }

    public function testGetsHelp(): void
    {
        $this->assertSame('Please upgrade.', $this->requirement->getHelp());
    }
}
