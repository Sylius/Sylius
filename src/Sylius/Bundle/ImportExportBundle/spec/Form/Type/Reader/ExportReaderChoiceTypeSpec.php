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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ExportReaderChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('testReader' => 'TestReader'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ImportExportBundle\Form\Type\Reader\ExportReaderChoiceType');
    }

    function it_sets_default_options(OptionsResolverInterface $resolver)
    {
        $readers = array('testReader' => 'TestReader');
        $resolver->setDefaults(array('choices' => $readers))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_export_reader_choice');
    }
}
