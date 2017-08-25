<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Grid\FieldTypes;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DatetimeFieldTypeSpec extends ObjectBehavior
{
    function let(DataExtractorInterface $dataExtractor): void
    {
        $this->beConstructedWith($dataExtractor);
    }

    function it_is_a_grid_field_type(): void
    {
        $this->shouldImplement(FieldTypeInterface::class);
    }

    function it_uses_data_extractor_to_obtain_data_parse_it_with_given_configuration_and_renders_it(
        DataExtractorInterface $dataExtractor,
        \DateTime $dateTime,
        Field $field
    ): void {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn($dateTime);

        $dateTime->format('Y-m-d')->willReturn('2001-10-10');

        $this->render($field, ['foo' => 'bar'], [
            'format' => 'Y-m-d',
        ])->shouldReturn('2001-10-10');
    }

    function it_returns_null_if_property_accessor_returns_null(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn(null);

        $this->render($field, ['foo' => 'bar'], [
            'format' => ''
        ])->shouldReturn(null);
    }

    function it_throws_exception_if_returned_value_is_not_datetime(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('render', [$field, ['foo' => 'bar'], [
                'format' => '',
            ]])
        ;
    }
}
