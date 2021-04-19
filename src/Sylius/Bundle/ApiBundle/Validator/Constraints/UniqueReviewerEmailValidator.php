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

use Sylius\Bundle\AdminApiBundle\Model\UserInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class UniqueReviewerEmailValidator extends ConstraintValidator
{
    /** @var UserRepositoryInterface */
    private $shopUserRepository;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(UserRepositoryInterface $shopUserRepository, UserContextInterface $userContext)
    {
        $this->shopUserRepository = $shopUserRepository;
        $this->userContext = $userContext;
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        /** @var UniqueReviewerEmail $constraint */
        Assert::isInstanceOf($constraint, UniqueReviewerEmail::class);

        if ($value === $this->getAuthenticatedUserEmail()) {
            return;
        }

        if ($this->shopUserRepository->findOneByEmail($value) !== null) {
            $this->context->addViolation($constraint->message);
        }
    }

    private function getAuthenticatedUserEmail(): ?string
    {
        /** @var UserInterface|null $user */
        $user = $this->userContext->getUser();
        if ($user !== null && $user instanceof ShopUserInterface) {
            return $user->getEmail();
        }

        return null;
    }
}
