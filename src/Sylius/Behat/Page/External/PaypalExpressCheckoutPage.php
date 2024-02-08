<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\External;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\Page;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class PaypalExpressCheckoutPage extends Page implements PaypalExpressCheckoutPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        private RepositoryInterface $securityTokenRepository,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function authorize()
    {
        $this->getDriver()->visit($this->findAuthorizeToken()->getTargetUrl() . '?token=EC-2d9EV13959UR209410U&PayerID=UX8WBNYWGBVMG');
    }

    public function pay()
    {
        $this->getDriver()->visit($this->findCaptureToken()->getTargetUrl() . '?token=EC-2d9EV13959UR209410U&PayerID=UX8WBNYWGBVMG');
    }

    public function cancel()
    {
        $this->getDriver()->visit($this->findCaptureToken()->getTargetUrl() . '?token=EC-2d9EV13959UR209410U&cancelled=1');
    }

    protected function getUrl(array $urlParameters = []): string
    {
        return 'https://www.sandbox.paypal.com';
    }

    protected function verifyUrl(array $urlParameters = []): void
    {
        $position = strpos($this->getSession()->getCurrentUrl(), $this->getUrl($urlParameters));
        if (0 !== $position) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    /**
     * @return TokenInterface
     *
     * @throws \RuntimeException
     */
    private function findAuthorizeToken()
    {
        return $this->findToken('authorize');
    }

    /**
     * @return TokenInterface
     *
     * @throws \RuntimeException
     */
    private function findCaptureToken()
    {
        return $this->findToken('capture');
    }

    /**
     * @param string $name
     *
     * @return TokenInterface
     */
    private function findToken($name)
    {
        $tokens = $this->securityTokenRepository->findAll();

        foreach ($tokens as $token) {
            if (strpos($token->getTargetUrl(), $name)) {
                return $token;
            }
        }

        throw new \RuntimeException(sprintf('Cannot find "%s" token, check if you are after proper checkout steps', $name));
    }
}
