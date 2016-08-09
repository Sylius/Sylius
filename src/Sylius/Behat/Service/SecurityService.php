<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SecurityService implements SecurityServiceInterface
{
    const ADMIN_PROVIDER_KEY = 'sylius_admin_user_provider';
    const ADMIN_SESSION_VARIABLE = '_security_admin';
    const SHOP_PROVIDER_KEY = 'sylius_shop_user_provider';
    const SHOP_SESSION_VARIABLE = '_security_shop';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CookieSetterInterface
     */
    private $cookieSetter;

    /**
     * @param SessionInterface $session
     * @param CookieSetterInterface $cookieSetter
     */
    public function __construct(SessionInterface $session, CookieSetterInterface $cookieSetter)
    {
        $this->session = $session;
        $this->cookieSetter = $cookieSetter;
    }

    /**
     * {@inheritdoc}
     */
    public function logShopUserIn(ShopUserInterface $shopUser)
    {
        $this->logUserIn($shopUser, self::SHOP_PROVIDER_KEY, self::SHOP_SESSION_VARIABLE);
    }

    public function logShopUserOut()
    {
        $this->logUserOut(self::SHOP_SESSION_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function logAdminUserIn(AdminUserInterface $adminUser)
    {
        $this->logUserIn($adminUser, self::ADMIN_PROVIDER_KEY, self::ADMIN_SESSION_VARIABLE);
    }

    public function logAdminUserOut()
    {
        $this->logUserOut(self::ADMIN_SESSION_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function performActionAsShopUser(ShopUserInterface $shopUser, callable $action)
    {
        $previousToken = $this->getShopUserToken();
        $this->logShopUserIn($shopUser);
        $action();
        $this->restorePreviousSessionTokenOrLogOut($previousToken, self::SHOP_SESSION_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function performActionAsAdminUser(AdminUserInterface $adminUser, callable $action)
    {
        $previousToken = $this->getAdminUserToken();
        $this->logAdminUserIn($adminUser);
        $action();
        $this->restorePreviousSessionTokenOrLogOut($previousToken, self::ADMIN_SESSION_VARIABLE);
    }

    /**
     * @param BaseUserInterface $user
     * @param string $providerKey
     * @param string $sessionTokenVariable
     */
    private function logUserIn(BaseUserInterface $user, $providerKey, $sessionTokenVariable)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());
        $serializedToken = serialize($token);

        $this->setSerializedToken($sessionTokenVariable, $serializedToken);

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    private function logUserOut($sessionTokenVariable)
    {
        $this->setSerializedToken($sessionTokenVariable, null);

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    /**
     * @param string $previousToken
     * @param string $sessionTokenVariable
     */
    private function restorePreviousSessionTokenOrLogOut($previousToken, $sessionTokenVariable)
    {
        if (null !== $previousToken) {
            $this->setSerializedToken($sessionTokenVariable, $previousToken);
            $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());

            return;
        }

        $this->logUserOut($sessionTokenVariable);
    }

    /**
     * @param string $sessionTokenVariable
     * @param string $token
     */
    private function setSerializedToken($sessionTokenVariable, $token)
    {
        $this->session->set($sessionTokenVariable, $token);
        $this->session->save();
    }

    /**
     * @return string
     */
    private function getShopUserToken()
    {
        return $this->session->get(self::SHOP_SESSION_VARIABLE);
    }

    /**
     * @return string
     */
    private function getAdminUserToken()
    {
        return $this->session->get(self::ADMIN_SESSION_VARIABLE);
    }
}
