<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * {@inheritdoc}
     *
     * @param SessionInterface $session
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        HttpUtils $httpUtils,
        $targetUrl,
        SessionInterface $session,
        ChannelContextInterface $channelContext
    ) {
        parent::__construct($httpUtils, $targetUrl);

        $this->session = $session;
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        $channel = $this->channelContext->getChannel();
        $this->session->remove('_sylius.cart.'.$channel->getCode());

        return parent::onLogoutSuccess($request);
    }
}
