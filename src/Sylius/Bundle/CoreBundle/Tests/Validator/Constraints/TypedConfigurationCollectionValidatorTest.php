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

use Sylius\Bundle\CoreBundle\Validator\Constraints\TypedConfiguration;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TypedConfigurationCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TypedConfigurationCollectionValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    /**
     * @test
     * @dataProvider violationCases
     */
    public function it_validates_against_constraints_with_invalid_data(
        int $expectedViolationCount,
        string $expectedPropertyPath,
        string $expectedMessage,
        $toValidate,
        Constraint $constraint
    ): void {
        $violations = $this->validator->validate($toValidate, $constraint);
        $this->assertCount($expectedViolationCount, $violations);
        $this->assertStringContainsString($expectedPropertyPath, $violations->get(0)->getPropertyPath());
        $this->assertSame($expectedMessage, $violations->get(0)->getMessage());
    }

    /**
     * @test
     * @dataProvider noViolationCases
     */
    public function it_validates_against_constraints_with_valid_data(
        $toValidate,
        Constraint $constraint
    ): void {
        $violations = $this->validator->validate($toValidate, $constraint);
        $this->assertCount(0, $violations);
    }

    public function violationCases(): iterable
    {
        yield [
            'expectedViolationCount' => 1,
            'expectedPropertyPath' => 'someValue',
            'expectedMessage' => 'This value should be between 1 and 10.',
            'toValidate' => [
                'type' => 'some_custom_type',
                'configuration' => [
                    'someValue' => 0
                ]
            ],
            'constraint' => new TypedConfigurationCollection([
                'types' => [
                    'some_custom_type' => new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    'some_custom_class' => new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'expectedViolationCount' => 1,
            'expectedPropertyPath' => 'someField',
            'expectedMessage' => 'This field is missing.',
            'toValidate' => new class() implements TypedConfiguration {
                public function getType(): string { return 'some_custom_class'; }
                public function getConfiguration(): array { return []; }
            },
            'constraint' => new TypedConfigurationCollection([
                'types' => [
                    'some_custom_type' => new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    'some_custom_class' => new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
    }

    public function noViolationCases(): iterable
    {
        yield [
            'toValidate' => [
                'type' => 'some_custom_type',
                'configuration' => [
                    'someValue' => 5
                ]
            ],
            'constraint' => new TypedConfigurationCollection([
                'types' => [
                    'some_custom_type' => new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    'some_custom_class' => new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'toValidate' => new class() implements TypedConfiguration {
                public function getType(): string { return 'some_custom_class'; }
                public function getConfiguration(): array { return ['someField' => 123]; }
            },
            'constraint' => new TypedConfigurationCollection([
                'types' => [
                    'some_custom_type' => new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    'some_custom_class' => new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'toValidate' => new class() {},
            'constraint' => new TypedConfigurationCollection([
                'types' => [
                    'some_custom_type' => new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    'some_custom_class' => new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
        yield [
            'toValidate' => [],
            'constraint' => new TypedConfigurationCollection([
                'types' => [
                    'some_custom_type' => new Collection([
                        'someValue' => new Range(['min' => 1, 'max' => 10])
                    ]),
                    'some_custom_class' => new Collection([
                        'someField' => new NotBlank()
                    ])
                ],
            ])
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->validator = $this->getContainer()->get('validator');
    }
}
