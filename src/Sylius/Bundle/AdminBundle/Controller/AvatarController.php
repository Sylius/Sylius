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

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\AvatarRepository;
use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class AvatarController
{
    /** @var AvatarRepository */
    private $avatarRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var RouterInterface */
    private $router;

    public function __construct(RepositoryInterface $avatarRepository, EngineInterface $templatingEngine, RouterInterface $router)
    {
        $this->avatarRepository = $avatarRepository;
        $this->templatingEngine = $templatingEngine;
        $this->router = $router;
    }

    public function removeAvatarAction(Request $request, string $id): Response
    {
        /** @var AvatarImage $avatar */
        $avatar = $this->avatarRepository->findOneByOwner($id);
        Assert::notNull($avatar);
        $this->avatarRepository->remove($avatar);

        $url = $this->router->generate('sylius_admin_admin_user_update', ['id' => $id]);

        return new RedirectResponse($url);
    }
}
