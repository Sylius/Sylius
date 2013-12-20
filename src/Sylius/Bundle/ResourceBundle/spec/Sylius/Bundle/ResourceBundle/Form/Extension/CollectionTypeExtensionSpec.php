<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Extension;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionTypeExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Extension\CollectionTypeExtension');
    }

    public function it_should_extends_abstract_type_extension()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractTypeExtension');
    }

    public function it_should_have_collection_as_extended_type()
    {
        $this->getExtendedType()->shouldReturn('collection');
    }

    public function it_should_have_default_option(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array(
            'button_add_label',
            'item_by_line',
        ))->shouldBeCalled();

        $resolver->setAllowedTypes(array(
            'item_by_line' => array('integer')
        ))->shouldBeCalled();

        $resolver->setDefaults(array(
            'button_add_label' => 'form.collection.add',
            'item_by_line' => 1,
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
