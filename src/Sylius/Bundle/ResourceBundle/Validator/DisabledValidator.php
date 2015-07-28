<?php
 
namespace Sylius\Bundle\ResourceBundle\Validator;

use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class DisabledValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     *
     * @param ToggleableInterface $value
     * @param Constraints\Disabled $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $this->ensureValueImplementsToggleableInterface($value);

        if ($value->isEnabled()) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param mixed $value
     */
    private function ensureValueImplementsToggleableInterface($value)
    {
        if (!($value instanceof ToggleableInterface)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" validates "%s" instances only',
                __CLASS__, 'Sylius\Component\Resource\Model\ToggleableInterface'
            ));
        }
    }
}
