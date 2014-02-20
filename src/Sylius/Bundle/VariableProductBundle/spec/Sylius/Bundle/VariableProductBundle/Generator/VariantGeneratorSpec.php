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

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantGeneratorSpec extends ObjectBehavior
{
    function let(
        ValidatorInterface $validator,
        ObjectRepository $variantRepository,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->beConstructedWith($validator, $variantRepository, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariableProductBundle\Generator\VariantGenerator');
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement('Sylius\Bundle\VariableProductBundle\Generator\VariantGeneratorInterface');
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
