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
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidDateAttributeConfiguration;
use Symfony\Component\Validator\Constraint;

final class ValidDateAttributeConfigurationTest extends TestCase
{
    private ValidDateAttributeConfiguration $validDateAttributeConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validDateAttributeConfiguration = new ValidDateAttributeConfiguration();
    }

    public function testHasTargets(): void
    {
        self::assertSame(Constraint::CLASS_CONSTRAINT, $this->validDateAttributeConfiguration->getTargets());
    }

    public function testValidatedBySpecificValidator(): void
    {
        self::assertSame('sylius_valid_date_attribute_validator', $this->validDateAttributeConfiguration->validatedBy());
    }

    public function testAvailableFormatsAreSupportedByTheIntlDateFormatter(): void
    {
        self::assertSame(
            ValidDateAttributeConfiguration::AVAILABLE_FORMATS,
            array_values(array_filter(
                ValidDateAttributeConfiguration::AVAILABLE_FORMATS,
                fn (string $format): bool => \defined(sprintf('IntlDateFormatter::%s', strtoupper($format))),
            )),
        );
    }
}
