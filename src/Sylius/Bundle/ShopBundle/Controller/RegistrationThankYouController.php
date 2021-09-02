<?php
declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class RegistrationThankYouController
{
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var RouterInterface */
    private $router;

    /** @var Environment */
    private $twig;

    /**
     * RegistrationThankYouController constructor.
     * @param Environment $twig
     * @param ChannelContextInterface $channelContext
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig, ChannelContextInterface $channelContext, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->channelContext = $channelContext;
        $this->router = $router;
    }

    public function thankYouAction()
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);
        if ($channel->isAccountVerificationRequired()) {
            return new Response($this->twig->render('@SyliusShop/registerThankYou.html.twig'));
        }
        return new RedirectResponse($this->router->generate('sylius_shop_account_dashboard'));
    }
}
