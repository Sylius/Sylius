<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariableProductBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Component\VariableProduct\Model\VariantInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantToIdentifierTransformerSpec extends ObjectBehavior
{

    function let(ObjectRepository $variantRepository)
    {
        $this->beConstructedWith($variantRepository, 'presentation');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Form\DataTransformer\VariantToIdentifierTransformer');
    }

    function it_returns_empty_string_if_null_transormed()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_throws_exception_if_not_Sylius_variant_transformed()
    {
        $variant = new \stdClass();

        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform($variant)
        ;
    }

    function it_transforms_variant_into_its_identifier_value(VariantInterface $variant)
    {
        $variant->getPresentation()->willReturn('IPHONE5BLACK');

        $this->transform($variant)->shouldReturn('IPHONE5BLACK');
    }

    function it_returns_null_if_empty_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_returns_null_if_variant_not_found_on_reverse_transform($variantRepository)
    {
        $variantRepository
            ->findOneBy(array('presentation' => 'IPHONE5WHITE'))
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->reverseTransform('IPHONE5WHITE')->shouldReturn(null);
    }

    function it_returns_variant_if_found_on_reverse_transform(VariantInterface $variantRepository, $variant)
    {
        $variantRepository
            ->findOneBy(array('presentation' => 'IPHONE5BLACK'))
            ->shouldBeCalled()
            ->willReturn($variant)
        ;

        $this->reverseTransform('IPHONE5BLACK')->shouldReturn($variant);
    }
}
