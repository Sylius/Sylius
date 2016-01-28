<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use Sylius\Behat\Page\User\LoginPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserContextSpec extends ObjectBehavior
{
    function let(LoginPage $loginPage)
    {
        $this->beConstructedWith($loginPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\UserContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_logs_in_user_with_given_credentials($loginPage)
    {
        $loginPage->open()->shouldBeCalled();
        $loginPage->logIn('john.doe@example.com', 'password123')->shouldBeCalled();

        $this->iLogInAs('john.doe@example.com', 'password123');
    }
}
