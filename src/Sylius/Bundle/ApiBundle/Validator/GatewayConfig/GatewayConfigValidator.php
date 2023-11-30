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

namespace Sylius\Bundle\ApiBundle\Validator\GatewayConfig;

use Sylius\Bundle\ApiBundle\Validator\Constraints\GatewayConfig;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class GatewayConfigValidator extends ConstraintValidator
{
    public const OFFLINE_GATEWAY_FACTORY = 'offline';

    /**
     * @param array<string, string> $factoryNames
     * @param array<string> $validationGroups
     */
    public function __construct(private array $factoryNames, private array $validationGroups)
    {
    }

    /** @param GatewayConfigInterface $value */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof GatewayConfigInterface) {
            throw new UnexpectedValueException($value, GatewayConfigInterface::class);
        }

        if (!$constraint instanceof GatewayConfig) {
            throw new UnexpectedTypeException($constraint, GatewayConfig::class);
        }

        /** @var string|null $factoryName */
        $factoryName = $value->getFactoryName();

        if ($factoryName === self::OFFLINE_GATEWAY_FACTORY || $factoryName === null) {
            return;
        }

        if (!in_array($factoryName, array_keys($this->factoryNames), true)) {
            $this->context->buildViolation($constraint->invalidGatewayFactory)
                ->atPath('factoryName')
                ->setParameter('{{ available_factories }}', implode(', ', array_keys($this->factoryNames)))
                ->addViolation()
            ;

            return;
        }

        if (!in_array($factoryName, $this->validationGroups, true)) {
            return;
        }

        /** @var string[] $groups */
        $groups = array_merge($constraint->groups, [$factoryName]);
        $validator = $this->context->getValidator()->inContext($this->context);
        $validator->validate(value: $value, groups: $groups);
    }
}
