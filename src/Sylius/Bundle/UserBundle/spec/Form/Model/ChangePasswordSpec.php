<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\UserBundle\Form\Model;

use PhpSpec\ObjectBehavior;

final class ChangePasswordSpec extends ObjectBehavior
{
    function it_has_current_password(): void
    {
        $this->setCurrentPassword('testPassword');
        $this->getCurrentPassword()->shouldReturn('testPassword');
    }

    function it_has_new_password(): void
    {
        $this->setNewPassword('testPassword');
        $this->getNewPassword()->shouldReturn('testPassword');
    }
}
