<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type\Export\Reader;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormBuilderInterface;
use Prophecy\Argument;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReaderTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\Export\Reader\UserReaderType');
    }

    function it_should_be_abstract_resource_type_object()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_form_with_proper_fields(FormBuilderInterface $builder)
    {
        $builder->add('batch_size', 'number', array(
            'label'      => 'sylius.form.reader.batch_size',
            'required' => true,
            'constraints' => array(
                new NotBlank(array('groups' => array('sylius'))),
            ),
        ))->willReturn($builder);
        $builder->add('date_format', 'text', array(
            'label'       => 'sylius.form.reader.date_format',
            'data'        => 'Y-m-d H:i:s',
            'required'    => true,
            'constraints' => array(
                new NotBlank(array('groups' => array('sylius'))),
            ),
        ))->willReturn($builder);

        $this->buildForm($builder, array());
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_user_orm_reader');
    }
}