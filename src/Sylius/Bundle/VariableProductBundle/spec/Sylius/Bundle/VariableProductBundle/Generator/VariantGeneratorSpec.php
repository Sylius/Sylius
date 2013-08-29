<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariableProductBundle\Generator;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantGeneratorSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Validator\ValidatorInterface $validator
     * @param Doctrine\Common\Persistence\ObjectRepository   $variantRepository
     */
    function let($validator, $variantRepository)
    {
        $this->beConstructedWith($validator, $variantRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Generator\VariantGenerator');
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement('Sylius\Bundle\VariableProductBundle\Generator\VariantGeneratorInterface');
    }

    /**
     * @param Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface $product
     */
    function it_throws_exception_if_product_doesnt_have_any_options($product)
    {
        $product->hasOptions()->willReturn(false);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringGenerate($product)
        ;
    }
}
