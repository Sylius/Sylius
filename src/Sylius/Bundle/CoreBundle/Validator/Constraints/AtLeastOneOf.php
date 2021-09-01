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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Composite;

/**
 * @internal
 */
class AtLeastOneOf extends Composite
{
    public const AT_LEAST_ONE_OF_ERROR = 'f27e6d6c-261a-4056-b391-6673a623531c';

    /**
     * @psalm-var array<array-key, string>
     */
    protected static $errorNames = [
        self::AT_LEAST_ONE_OF_ERROR => 'AT_LEAST_ONE_OF_ERROR',
    ];

    /**
     * @var Constraint[]
     */
    public $constraints = [];
    public string $message = 'This value should satisfy at least one of the following constraints:';
    public string $messageCollection = 'Each element of this collection should satisfy its own set of constraints.';
    public bool $includeInternalMessages = true;

    public function getDefaultOption(): string
    {
        return 'constraints';
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['constraints'];
    }

    protected function getCompositeOption(): string
    {
        return 'constraints';
    }
}
