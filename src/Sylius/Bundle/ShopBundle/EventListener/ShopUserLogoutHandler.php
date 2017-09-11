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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\ShopBundle\ShopSession;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ShopUserLogoutHandler extends DefaultLogoutSuccessHandler
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var string
     */
    private $cartSessionKey;

    /**
     * {@inheritdoc}
     *
     * @param SessionInterface $session
     * @param ChannelContextInterface $channelContext
     * @param string $cartSessionKey
     */
    public function __construct(
        HttpUtils $httpUtils,
        string $targetUrl,
        SessionInterface $session,
        ChannelContextInterface $channelContext,
        string $cartSessionKey
    ) {
        parent::__construct($httpUtils, $targetUrl);

        $this->session = $session;
        $this->channelContext = $channelContext;
        $this->cartSessionKey = $cartSessionKey;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request): Response
    {
        $channel = $this->channelContext->getChannel();
        $this->session->remove($this->cartSessionKey . $channel->getCode());

        return parent::onLogoutSuccess($request);
    }
}
