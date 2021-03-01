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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\ResetPasswordTokenAwareInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

/** @experimental */
class ResetPasswordTokenAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        /** @var ShopUserInterface $user */
        $user = $context['object_to_populate'];

        $object->setResetPasswordToken($user->getPasswordResetToken());

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof ResetPasswordTokenAwareInterface;
    }
}
