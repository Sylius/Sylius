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
use Sylius\Component\User\Model\User;

/**
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
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
    
    function it_sets_user_first_name()
    {
        $this->setFirstName('Edward');
        
        $this->getFirstName()->shouldReturn('Edward');
    }
    
    function it_sets_user_last_name()
    {
        $this->setLastName('Thatch');
        
        $this->getLastName()->shouldReturn('Thatch');
    }
    
    function it_can_get_full_name()
    {
        $this->setFirstName('Edward');
        $this->setLastName('Kenway');
        
        $this->getFullName()->shouldReturn('Edward Kenway');
    }
    
    function it_should_return_true_if_user_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);
        
        $this->isDeleted()->shouldReturn(true);
    }
    
    function it_should_return_false_if_user_is_not_deleted()
    {        
        $this->isDeleted()->shouldReturn(false);
    }
    
    function it_should_return_false_if_user_deleted_time_is_future_date()
    {
        $deletedAt = new \DateTime('tomorrow');
        $this->setDeletedAt($deletedAt);
        
        $this->isDeleted()->shouldReturn(false);
    }
}
