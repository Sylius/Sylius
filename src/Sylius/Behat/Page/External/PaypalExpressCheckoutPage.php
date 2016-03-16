<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\External;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Page;
use Sylius\Behat\Page\UnexpectedPageException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalExpressCheckoutPage extends Page
{
    /**
     * @param string $email
     * @param string $password
     *
     * @throws ElementNotFoundException
     */
    public function logIn($email, $password)
    {
        $hasLoginButton = $this->getDocument()->waitFor(15, function () {
            return $this->getDocument()->hasButton('login_button');
        });

        if ($hasLoginButton) {
            $this->getDocument()->pressButton('login_button');
        }

        $this->getDocument()->waitFor(15, function () {
            return $this->getDocument()->hasField('login_email');
        });

        $this->getDocument()->fillField('login_email', $email);
        $this->getDocument()->fillField('login_password', $password);
        $this->getDocument()->pressButton('submitLogin');
    }

    /**
     * @throws ElementNotFoundException
     */
    public function pay()
    {
        $this->getDocument()->waitFor(15, function () {
            return $this->getDocument()->hasButton('continue');
        });
        $this->getDocument()->pressButton('continue');
    }

    /**
     * @throws ElementNotFoundException
     */
    public function cancel()
    {
        $this->getDocument()->waitFor(15, function () {
            return $this->getDocument()->hasButton('cancel_return');
        });
        $this->getDocument()->pressButton('cancel_return');
    }

    /**
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException
     */
    public function verify(array $urlParameters = [])
    {
        $this->verifyUrl($urlParameters);
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        return 'https://www.sandbox.paypal.com';
    }

    /**
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException
     */
    protected function verifyUrl(array $urlParameters = [])
    {
        $position = strpos($this->getSession()->getCurrentUrl(), $this->getUrl($urlParameters));
        if (0 !== $position) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }
}
