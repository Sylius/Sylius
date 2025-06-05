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
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfiguration;
use Symfony\Component\Validator\Constraint;

final class ValidTextAttributeConfigurationTest extends TestCase
{
    private ValidTextAttributeConfiguration $validTextAttributeConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validTextAttributeConfiguration = new ValidTextAttributeConfiguration();
    }

    public function testHasTargets(): void
    {
        self::assertSame(Constraint::CLASS_CONSTRAINT, $this->validTextAttributeConfiguration->getTargets());
    }

    public function testValidatedBySpecificValidator(): void
    {
        self::assertSame(
            'sylius_valid_text_attribute_validator',
            $this->validTextAttributeConfiguration->validatedBy(),
        );
    }
}
