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

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Bundle\CoreBundle\Command\Shop\Account\VerifyShopUser;
use Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Type\UserResetPasswordType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @experimental
 */
final readonly class VerifyShopUserAction
{
    public function __construct(
        private RouterInterface $router,
        private UserRepositoryInterface $userRepository,
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(Request $request, string $token, ?string $redirect): Response
    {
        $redirect ??= 'sylius_shop_account_dashboard';

        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneBy(['emailVerificationToken' => $token]);

        if (null === $user) {
            $this->addErrorNotification();

            return new RedirectResponse($this->router->generate($redirect));
        }

        $this->messageBus->dispatch(new VerifyShopUser(
            $this->channelContext->getChannel()->getCode(),
            $this->localeContext->getLocaleCode(),
            $token,
        ));

        $this->addSuccessNotification();

        return new RedirectResponse($this->router->generate($redirect));
    }

    private function addSuccessNotification(): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('success', $this->translator->trans('sylius.user.verify_email', [], 'flashes'));
    }

    private function addErrorNotification(): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('error', $this->translator->trans('sylius.user.verify_email_by_invalid_token', [], 'flashes'));
    }
}
