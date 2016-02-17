<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormTypeInterface;

class ImageTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Image');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ImageType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }
}
