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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;

final class LoggedInShopUserIdAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    /**
     * @param ShopUserIdAwareInterface $object
     */
    public function transform($object, string $to, array $context = []): ShopUserIdAwareInterface
    {
        /** @var ShopUserInterface|UserInterface $user */
        $user = $this->userContext->getUser();

        if (!$user instanceof ShopUserInterface) {
            return $object;
        }

        $object->setShopUserId($user->getId());

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof ShopUserIdAwareInterface;
    }
}
