<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Entity;

use PHPSpec2\ObjectBehavior;

class Image extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Entity\Image');
    }

    function it_is_Sylius_image()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Image');
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Model\ImageInterface');
    }
}
