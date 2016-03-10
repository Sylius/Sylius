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
use Prophecy\Argument;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Sylius\Component\Grid\FieldTypes\StringFieldType;

/**
 * @mixin StringFieldType
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StringFieldTypeSpec extends ObjectBehavior
{
    function let(DataExtractorInterface $dataExtractor)
    {
        $this->beConstructedWith($dataExtractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\FieldTypes\StringFieldType');
    }
    
    function it_is_a_grid_field_type()
    {
        $this->shouldImplement(FieldTypeInterface::class);
    }

    function it_uses_data_extractor_to_obtain_data_and_renders_it(DataExtractorInterface $dataExtractor, Field $field)
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('Value');
        $this->render($field, ['foo' => 'bar'])->shouldReturn('Value');
    }
    
    function it_has_name()
    {
        $this->getName()->shouldReturn('string');
    }
}
