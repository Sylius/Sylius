<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Model;

use PhpSpec\ObjectBehavior;

/**

 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\User');
    }
    
    function it_implements_Fos_user_interface()
    {
        $this->shouldImplement('FOS\UserBundle\Model\UserInterface');
    }
}
