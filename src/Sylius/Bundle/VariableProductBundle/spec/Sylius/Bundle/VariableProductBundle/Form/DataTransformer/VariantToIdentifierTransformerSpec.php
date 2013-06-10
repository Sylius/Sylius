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

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantToIdentifierTransformerSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectRepository $variantRepository
     */
    function let($variantRepository)
    {
        $this->beConstructedWith($variantRepository, 'title');
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

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\VariantInterface $variant
     */
    function it_transforms_variant_into_its_identifier_value($variant)
    {
        $variant->getTitle()->willReturn('IPHONE5BLACK');

        $this->transform($variant)->shouldReturn('IPHONE5BLACK');
    }

    function it_returns_null_if_empty_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_returns_null_if_variant_not_found_on_reverse_transform($variantRepository)
    {
        $variantRepository
            ->findOneBy(array('title' => 'IPHONE5WHITE'))
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->reverseTransform('IPHONE5WHITE')->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\VariantInterface $variant
     */
    function it_returns_variant_if_found_on_reverse_transform($variantRepository, $variant)
    {
        $variantRepository
            ->findOneBy(array('title' => 'IPHONE5BLACK'))
            ->shouldBeCalled()
            ->willReturn($variant)
        ;

        $this->reverseTransform('IPHONE5BLACK')->shouldReturn($variant);
    }
}
