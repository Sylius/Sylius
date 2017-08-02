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

namespace Sylius\Bundle\ResourceBundle\Validator;

use Sylius\Component\Resource\Model\ToggleableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class DisabledValidator extends ConstraintValidator
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
                __CLASS__, ToggleableInterface::class
            ));
        }
    }
}
