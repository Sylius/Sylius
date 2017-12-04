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

final class StringFieldTypeSpec extends ObjectBehavior
{
    function let(DataExtractorInterface $dataExtractor): void
    {
        $this->beConstructedWith($dataExtractor);
    }

    function it_is_a_grid_field_type(): void
    {
        $this->shouldImplement(FieldTypeInterface::class);
    }

    function it_uses_data_extractor_to_obtain_data_and_renders_it(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('Value');
        $this->render($field, ['foo' => 'bar'], [])->shouldReturn('Value');
    }

    function it_escapes_string_values(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('<i class="book icon"></i>');
        $this->render($field, ['foo' => 'bar'], [])->shouldReturn('&lt;i class=&quot;book icon&quot;&gt;&lt;/i&gt;');
    }

    function it_does_not_escape_non_string_values(DataExtractorInterface $dataExtractor, Field $field): void
    {
        $data = new \stdClass();

        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn($data);
        $this->render($field, ['foo' => 'bar'], [])->shouldReturn($data);
    }
}
