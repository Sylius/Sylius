<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AssociationBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Association\Model\Association;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AssociationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Association::class, ['sylius'], 'product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AssociationBundle\Form\Type\AssociationType');
    }

    function it_extends_abstract_resource_type()
    {
        $this->shouldImplement(AbstractResourceType::class);
    }

    function it_builds_form(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('type', 'sylius_product_association_type_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($formBuilder)
        ;
        $formBuilder
            ->add('product', 'sylius_product_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($formBuilder)
        ;

        $this->buildForm($formBuilder, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_association');
    }
}
