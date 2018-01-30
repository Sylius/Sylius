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
use Sylius\Component\Core\URLRedirect\URLRedirectLoopDetectorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TypeError;

/**
 * Class URLRedirectValidator
 *
 * Validates the new URLRedirect entity by checking for redirect loops
 *
 * @package Sylius\Bundle\AdminBundle\Validator\Constraints
 * @see URLRedirectLoopDetector
 */
final class URLRedirectValidator extends ConstraintValidator
{
    /**
     * @var URLRedirectLoopDetectorInterface
     */
    private $redirectDetector;

    public function __construct(URLRedirectLoopDetectorInterface $redirectDetector)
    {
        $this->redirectDetector = $redirectDetector;
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
        if (!($value instanceof URLRedirect)) {
            throw new TypeError('The validator can only validate URLRoutes');
        }

        if ($value->getOldRoute() === $value->getNewRoute()) {
            $this->context->addViolation('sylius.url_redirects.self_reroute');
        }

        if ($this->redirectDetector->containsLoop($value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}