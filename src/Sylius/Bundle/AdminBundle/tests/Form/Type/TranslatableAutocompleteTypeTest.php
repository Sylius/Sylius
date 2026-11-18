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

namespace Tests\Sylius\Bundle\AdminBundle\Form\Type;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslatableAutocompleteTypeTest extends TestCase
{
    private LocaleContextInterface&MockObject $localeContext;

    private TranslatableAutocompleteType $type;

    public function testItReadsTheLocaleForEveryResolution(): void
    {
        $this->localeContext->method('getLocaleCode')->willReturnOnConsecutiveCalls('en_US', 'pl_PL');

        $resolver = $this->resolver();

        $this->assertSame('en_US', $resolver->resolve()['locale_code']);
        $this->assertSame(
            'pl_PL',
            $resolver->resolve()['locale_code'],
            'The form kept the locale of an earlier request.',
        );
    }

    protected function setUp(): void
    {
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->type = new TranslatableAutocompleteType($this->localeContext);
    }

    private function resolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        // supplied by the parent type in production
        $resolver->setDefault('extra_options', []);
        $this->type->configureOptions($resolver);

        return $resolver;
    }
}
