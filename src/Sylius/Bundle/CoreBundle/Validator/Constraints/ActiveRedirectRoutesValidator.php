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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TypeError;

/**
 * Class ActiveRedirectRoutesValidator
 *
 * Validates that there is only one active redirect
 */
final class ActiveRedirectRoutesValidator extends ConstraintValidator
{
    /**
     * @var URLRedirectRepositoryInterface
     */
    private $urlRedirectRepository;

    public function __construct(URLRedirectRepositoryInterface $urlRedirectRepository)
    {
        $this->urlRedirectRepository = $urlRedirectRepository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @throws TypeError
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($value instanceof URLRedirectInterface)) {
            throw new TypeError('The validator can only validate URLRoutes');
        }

        // Inactive routes don't cause conflicts
        if ($value->isEnabled() === false) {
            return;
        }

        $activeRoute = $this->urlRedirectRepository->getActiveRedirectForRoute($value->getOldRoute());

        if ($activeRoute !== null && $activeRoute !== $value) {
            $this->context->addViolation($constraint->message);
        }
    }
}
