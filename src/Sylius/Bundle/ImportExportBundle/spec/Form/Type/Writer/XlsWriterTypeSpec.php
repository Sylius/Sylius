<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ImportExportBundle\Form\Type\Reader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class XlsWriterTypeSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ImportExportBundle\Form\Type\Reader\XlsWriterType');
    }

    function it_builds_form_with_proper_fields(FormBuilderInterface $builder)
    {
        $builder
            ->add('file', 'text', Argument::type('array'))
            ->willReturn($builder)
        ;
        $this->buildForm($builder, array());
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_xls_writer');
    }
}