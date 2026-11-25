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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\AllowedImageMimeTypes;
use Sylius\Bundle\CoreBundle\Validator\Constraints\AllowedImageMimeTypesValidator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AllowedImageMimeTypesValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private AllowedImageMimeTypesValidator $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new AllowedImageMimeTypesValidator(['image/jpeg', 'image/webp']);
        $this->validator->initialize($this->executionContext);
    }

    public function testItDoesNothingIfValueIsNull(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, new AllowedImageMimeTypes());
    }

    public function testItDoesNothingIfMimeTypeIsAllowed(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(
            new File(__DIR__ . '/../../../Resources/fixtures/caps/cap_01.webp'),
            new AllowedImageMimeTypes(),
        );
    }

    public function testItAddsAViolationIfMimeTypeIsNotAllowed(): void
    {
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with('sylius.image.file.allowed_mime_types', ['%types%' => 'image/jpeg, image/webp'])
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate(new File(__FILE__), new AllowedImageMimeTypes());
    }

    public function testItDoesNothingIfTheUploadFailed(): void
    {
        $this->executionContext->expects($this->never())->method('buildViolation');

        // What PHP gives for an interrupted upload: no temporary file, hence an empty path
        $this->validator->validate(
            new UploadedFile('', 'photo.jpg', null, \UPLOAD_ERR_PARTIAL, true),
            new AllowedImageMimeTypes(),
        );
    }
}
