<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\VariableProduct\Generator;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Component\VariableProduct\Model\VariableProductInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantGeneratorSpec extends ObjectBehavior
{

    function let(ValidatorInterface $validator, ObjectRepository $variantRepository)
    {
        $this->beConstructedWith($validator, $variantRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\VariableProduct\Generator\VariantGenerator');
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement('Sylius\Component\VariableProduct\Generator\VariantGeneratorInterface');
    }

    function it_throws_exception_if_product_doesnt_have_any_options(VariableProductInterface $product)
    {
        $product->hasOptions()->willReturn(false);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringGenerate($product)
        ;
    }
}
