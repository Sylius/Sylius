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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

final class ShopUserLogoutHandler extends DefaultLogoutSuccessHandler
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CartStorageInterface
     */
    private $cartStorage;

    /**
     * {@inheritdoc}
     *
     * @param ChannelContextInterface $channelContext
     * @param CartStorageInterface $cartStorage
     */
    public function __construct(
        HttpUtils $httpUtils,
        string $targetUrl,
        ChannelContextInterface $channelContext,
        CartStorageInterface $cartStorage
    ) {
        parent::__construct($httpUtils, $targetUrl);

        $this->channelContext = $channelContext;
        $this->cartStorage = $cartStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request): Response
    {
        $channel = $this->channelContext->getChannel();
        $this->cartStorage->removeForChannel($channel);

        return parent::onLogoutSuccess($request);
    }
}
