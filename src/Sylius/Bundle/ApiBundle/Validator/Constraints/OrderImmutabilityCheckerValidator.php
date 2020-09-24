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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderImmutabilityCheckerValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    }

    public function validate($command, Constraint $constraint): void
    {
        Assert::isInstanceOf($command, OrderTokenValueAwareInterface::class);

        /** @var OrderImmutabilityChecker $constraint */
        Assert::isInstanceOf($constraint, OrderImmutabilityChecker::class);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $command->getOrderTokenValue()]);

        if($order->getCheckoutState() !== OrderCheckoutStates::STATE_COMPLETED){
            return;
        }
        dd('help');

        $this->context->addViolation($constraint->message);
    }
}
