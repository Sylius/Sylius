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

namespace Sylius\Bundle\AdminBundle\Action;

use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Repository\AvatarImageRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class RemoveAvatarAction
{
    public function __construct(
        private AvatarImageRepositoryInterface $avatarRepository,
        private RouterInterface $router,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $userId = $request->attributes->get('id', '');

        if (!$this->csrfTokenManager->isTokenValid(
            new CsrfToken($userId, (string) $request->query->get('_csrf_token', '')),
        )) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var AvatarImage|null $avatar */
        $avatar = $this->avatarRepository->findOneByOwnerId($userId);

        if (null !== $avatar) {
            $this->avatarRepository->remove($avatar);
        }

        return new RedirectResponse($this->router->generate('sylius_admin_admin_user_update', ['id' => $userId]));
    }
}
