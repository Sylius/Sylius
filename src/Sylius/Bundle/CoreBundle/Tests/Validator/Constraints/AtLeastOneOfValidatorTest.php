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

namespace Sylius\Bundle\CoreBundle\Tests\Validator\Constraints;

use Sylius\Bundle\CoreBundle\Validator\Constraints\AtLeastOneOf;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AtLeastOneOfValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    /**
     * @test
     * @dataProvider cases
     */
    public function it_validates_against_constraints_with_invalid_data(
        int $expectedViolationCount,
        $toValidate,
        Constraint $constraint
    ): void {
        $violations = $this->validator->validate($toValidate, $constraint);
        $this->assertCount($expectedViolationCount, $violations);
    }

    public function cases(): iterable
    {
        yield [
            'expectedViolationCount' => 1,
            'toValidate' => ['someValue' => 0],
            'constraint' => new AtLeastOneOf([
                'constraints' => [
                    new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'expectedViolationCount' => 1,
            'toValidate' => [],
            'constraint' => new AtLeastOneOf([
                'constraints' => [
                    new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'expectedViolationCount' => 1,
            'toValidate' => ['someField' => ''],
            'constraint' => new AtLeastOneOf([
                'constraints' => [
                    new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'expectedViolationCount' => 0,
            'toValidate' => ['someField' => 'walter white'],
            'constraint' => new AtLeastOneOf([
                'constraints' => [
                    new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::bootKernel()->getContainer();
        $this->validator = $container->get('validator');
    }
}
