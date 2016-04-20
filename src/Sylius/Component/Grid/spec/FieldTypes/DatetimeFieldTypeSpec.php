<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\FieldTypes;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\DatetimeFieldType;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;

/**
 * @mixin DatetimeFieldType
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DatetimeFieldTypeSpec extends ObjectBehavior
{
    function let(DataExtractorInterface $dataExtractor)
    {
        $this->beConstructedWith($dataExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\FieldTypes\DatetimeFieldType');
    }

    function it_is_a_grid_field_type()
    {
        $this->shouldImplement(FieldTypeInterface::class);
    }

    function it_uses_data_extractor_to_obtain_data_parse_it_with_given_configuration_and_renders_it(
        DataExtractorInterface $dataExtractor,
        \DateTime $dateTime,
        Field $field
    ) {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn($dateTime);
        $field->getOptions()->willReturn(['format' => 'Y-m-d']);

        $dateTime->format('Y-m-d')->willReturn('2001-10-10');

        $this->render($field, ['foo' => 'bar'])->shouldReturn('2001-10-10');
    }

    function it_returns_null_if_property_accessor_returns_null(DataExtractorInterface $dataExtractor, Field $field)
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn(null);
        $field->getOptions()->willReturn(['format' => '']);

        $this->render($field, ['foo' => 'bar'])->shouldReturn(null);
    }

    function it_throws_exception_if_returned_value_is_not_datetime(DataExtractorInterface $dataExtractor, Field $field)
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('badObject');
        $field->getOptions()->willReturn(['format' => '']);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('render', [$field, ['foo' => 'bar']])
        ;
    }

    function it_throws_exception_if_format_option_is_not_set(Field $field)
    {
        $field->getOptions()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('render', [$field, ['foo' => 'bar']])
        ;
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('datetime');
    }
}
