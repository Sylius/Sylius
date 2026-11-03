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

namespace Tests\Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeValueTranslationsType;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AllowMockObjectsWithoutExpectations]
final class SelectAttributeValueTranslationsTypeTest extends TestCase
{
    public function testReflectsUpdatedLocalesFromProviderAcrossCalls(): void
    {
        $provider = $this->createMock(TranslationLocaleProviderInterface::class);
        $provider->expects(self::exactly(2))
            ->method('getDefinedLocalesCodes')
            ->willReturnOnConsecutiveCalls(['en_US', 'pl_PL'], ['en_US', 'pl_PL', 'de_DE']);
        $provider->method('getDefaultLocaleCode')->willReturn('en_US');

        $formType = new SelectAttributeValueTranslationsType($provider);

        $resolver = new OptionsResolver();
        $formType->configureOptions($resolver);
        self::assertSame(['en_US', 'pl_PL'], $resolver->resolve()['entries']);
        self::assertSame(['en_US', 'pl_PL', 'de_DE'], $resolver->resolve()['entries']);
    }
}
