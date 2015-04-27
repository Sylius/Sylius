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
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class UserSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\User');
    }

    public function it_implements_user_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Model\UserInterface');
    }

    public function it_should_return_true_if_user_is_deleted()
    {
        $deletedAt = new \DateTime('yesterday');
        $this->setDeletedAt($deletedAt);

        $this->shouldBeDeleted();
    }

    public function it_should_return_false_if_user_is_not_deleted()
    {
        $this->shouldNotBeDeleted();
    }

    public function it_should_return_false_if_user_deleted_time_is_future_date()
    {
        $deletedAt = new \DateTime('tomorrow');
        $this->setDeletedAt($deletedAt);

        $this->shouldNotBeDeleted();
    }
}
