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

final class PasswordResetRequestSpec extends ObjectBehavior
{
    function it_has_email(): void
    {
        $this->setEmail('test@example.com');
        $this->getEmail()->shouldReturn('test@example.com');
    }
}
