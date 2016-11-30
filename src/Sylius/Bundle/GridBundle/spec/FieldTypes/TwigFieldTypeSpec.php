<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\GridBundle\FieldTypes;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\GridBundle\FieldTypes\TwigFieldType;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TwigFieldTypeSpec extends ObjectBehavior
{
    function let(DataExtractorInterface $dataExtractor, \Twig_Environment $twig)
    {
        $this->beConstructedWith($dataExtractor, $twig);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TwigFieldType::class);
    }

    function it_is_a_grid_field_type()
    {
        $this->shouldImplement(FieldTypeInterface::class);
    }

    function it_uses_data_extractor_to_obtain_data_and_renders_it_via_twig(
        DataExtractorInterface $dataExtractor,
        \Twig_Environment $twig,
        Field $field
    ) {
        $field->getPath()->willReturn('foo');

        $dataExtractor->get($field, ['foo' => 'bar'])->willReturn('Value');
        $twig->render('foo.html.twig', ['data' => 'Value', 'options' => ['template' => 'foo.html.twig']])->willReturn('<html>Value</html>');

        $this->render($field, ['foo' => 'bar'], [
            'template' => 'foo.html.twig',
        ])->shouldReturn('<html>Value</html>');
    }

    function it_uses_data_directly_if_dot_is_configured_as_path(
        \Twig_Environment $twig,
        Field $field
    ) {
        $field->getPath()->willReturn('.');
        $twig->render('foo.html.twig', ['data' => 'bar', 'options' => ['template' => 'foo.html.twig']])->willReturn('<html>Bar</html>');

        $this->render($field, 'bar', ['template' => 'foo.html.twig'])->shouldReturn('<html>Bar</html>');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('twig');
    }
}
