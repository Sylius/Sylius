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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\DateAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\DatetimeAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidDateAttributeConfiguration;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Test\TypeTestCase;

final class DateAttributeConfigurationTypeTest extends TypeTestCase
{
    #[Test]
    #[DataProvider('configurationTypesProvider')]
    public function it_offers_only_the_formats_supported_by_the_intl_date_formatter(string $formType): void
    {
        $view = $this->factory->create($formType)->createView();

        self::assertSame(
            ValidDateAttributeConfiguration::AVAILABLE_FORMATS,
            array_map(fn (ChoiceView $choiceView): string => $choiceView->value, $view['format']->vars['choices']),
        );
    }

    #[Test]
    #[DataProvider('configurationTypesProvider')]
    public function it_submits_the_chosen_format(string $formType): void
    {
        $form = $this->factory->create($formType);
        $form->submit(['format' => 'short']);

        self::assertTrue($form->isSynchronized());
        self::assertSame(['format' => 'short'], $form->getData());
    }

    #[Test]
    #[DataProvider('configurationTypesProvider')]
    public function it_submits_no_format_when_none_is_chosen(string $formType): void
    {
        $form = $this->factory->create($formType);
        $form->submit(['format' => '']);

        self::assertTrue($form->isSynchronized());
        self::assertSame(['format' => null], $form->getData());
    }

    public static function configurationTypesProvider(): iterable
    {
        yield 'date' => [DateAttributeConfigurationType::class];
        yield 'datetime' => [DatetimeAttributeConfigurationType::class];
    }
}
