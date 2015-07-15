<?php

/*
 * This file is part of the NIM package.
 *
 * (c) Langlade Arnaud
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Extension;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class CollectionExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Extension\CollectionExtension');
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
            'button_delete_label',
        ))->shouldBeCalled();

        $resolver->setDefaults(array(
            'button_add_label' => 'form.collection.add',
            'button_delete_label' => 'form.collection.delete',
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
