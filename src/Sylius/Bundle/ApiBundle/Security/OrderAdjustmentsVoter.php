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

namespace Sylius\Bundle\ApiBundle\Security;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class OrderAdjustmentsVoter extends Voter
{
    public function __construct(private AdjustmentOrderProviderInterface $adjustmentOrderProvider)
    {
    }

    public const SYLIUS_ORDER_ADJUSTMENT = 'SYLIUS_ORDER_ADJUSTMENT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Collection;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return self::SYLIUS_ORDER_ADJUSTMENT === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($subject === [] || $subject->isEmpty() || !$subject->first() instanceof AdjustmentInterface) {
            return true;
        }

        /** @var AdjustmentInterface $subjectItem */
        foreach ($subject as $subjectItem) {
            if ($this->adjustmentOrderProvider->provide($subjectItem)) {
                /** @var OrderInterface $order */
                $order = $this->adjustmentOrderProvider->provide($subjectItem);

                if (!$order->isCreatedByGuest() || $order->getUser()) {
                    return $order->getUser() === $user;
                }

                break;
            }
        }

        return true;
    }
}
