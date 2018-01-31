<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 12:43
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;


use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;
use Sylius\Component\Core\URLRedirect\URLRedirectLoopDetectorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TypeError;

/**
 * Class ActiveRedirectRoutesValidator
 *
 * Validates that there is only one active redirect
 *
 * @package Sylius\Bundle\AdminBundle\Validator\Constraints
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

        if ($activeRoute !== null) {
            $this->context->addViolation($constraint->message);
        }
    }
}