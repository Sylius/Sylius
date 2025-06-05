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

namespace Tests\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidSelectAttributeConfiguration;
use Symfony\Component\Validator\Constraint;

final class ValidSelectAttributeConfigurationTest extends TestCase
{
    private ValidSelectAttributeConfiguration $validSelectAttributeConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validSelectAttributeConfiguration = new ValidSelectAttributeConfiguration();
    }

    public function testHasTargets(): void
    {
        self::assertSame(Constraint::CLASS_CONSTRAINT, $this->validSelectAttributeConfiguration->getTargets());
    }

    public function testValidatedBySpecificValidator(): void
    {
        self::assertSame('sylius_valid_select_attribute_validator', $this->validSelectAttributeConfiguration->validatedBy());
    }
}
