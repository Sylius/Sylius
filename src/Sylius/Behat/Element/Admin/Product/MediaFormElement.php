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

namespace Sylius\Behat\Element\Admin\Product;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class MediaFormElement extends BaseFormElement implements MediaFormElementInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        protected readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function attachImage(string $path, ?string $type = null, ?ProductVariantInterface $productVariant = null): void
    {
        $this->changeTab();

        $this->getElement('add_image')->click();

        $this->waitForFormUpdate();

        $images = $this->getElement('images');
        $imagesSubform = $images->findAll('css', '[data-test-image-subform]');
        $imageSubform = end($imagesSubform);

        if (null !== $type) {
            $imageSubform->fillField('Type', $type);
        }

        if (null !== $productVariant) {
            $this->autocompleteHelper->selectByValue(
                $this->getDriver(),
                $imageSubform->find('css', '[data-test-product-variant]')->getXpath(),
                $productVariant->getCode(),
            );
        }

        $filesPath = $this->getParameter('files_path');
        $imageSubform->find('css', '[data-test-file]')->attachFile($filesPath . $path);
    }

    public function changeImageWithType(string $type, string $path): void
    {
        $filesPath = $this->getParameter('files_path');

        $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        $imageSubform->find('css', '[data-test-file]')->attachFile($filesPath . $path);
    }

    public function removeImageWithType(string $type): void
    {
        $this->changeTab();

        $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        $imageSubform->find('css', '[data-test-image-delete]')->click();
        $this->waitForFormUpdate();
    }

    public function removeFirstImage(): void
    {
        $this->changeTab();

        $firstSubform = $this->getFirstImageSubform();
        $firstSubform->find('css', '[data-test-image-delete]')->click();
        $this->waitForFormUpdate();
    }

    public function hasImageWithType(string $type): bool
    {
        $this->changeTab();

        try {
            $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        } catch (ElementNotFoundException) {
            return false;
        }

        $imageUrl = $imageSubform->getAttribute('data-test-image-url');
        $this->getDriver()->visit($imageUrl);
        $statusCode = $this->getDriver()->getStatusCode();
        $this->getDriver()->back();

        return in_array($statusCode, [200, 304], true);
    }

    public function hasImageWithVariant(ProductVariantInterface $productVariant): bool
    {
        $this->changeTab();

        $images = $this->getElement('images');

        return $images->has('css', sprintf('[data-test-product-variant*="%s"]', $productVariant->getCode()));
    }

    public function countImages(): int
    {
        $images = $this->getElement('images');
        $imageSubforms = $images->findAll('css', '[data-test-image-subform]');

        return count($imageSubforms);
    }

    public function getImages(): array
    {
        $images = $this->getElement('images');

        return $images->findAll('css', '[data-test-image-subform]');
    }

    public function assertImageTypeAndPosition($image, string $expectedType, int $expectedPosition): void
    {
        $type = $image->find('css', 'input[data-test-type]')->getValue();
        $position = $image->find('css', 'input[data-test-position]')->getValue();

        if (!$type || !$position) {
            throw new \Exception('Type or position element not found in the image subform.');
        }

        if ($type !== $expectedType) {
            throw new \Exception(sprintf('Expected type "%s", but got "%s".', $expectedType, $type));
        }

        if ((int) $position !== $expectedPosition) {
            throw new \Exception(sprintf('Expected position "%d", but got "%d".', $expectedPosition, $position));
        }
    }

    public function modifyFirstImageType(string $type): void
    {
        $this->changeTab();

        $firstImageSubform = $this->getFirstImageSubform();

        $firstImageSubform->find('css', 'input[data-test-type]')->setValue($type);
    }

    public function modifyFirstImagePosition(int $position): void
    {
        $this->changeTab();

        $firstImageSubform = $this->getFirstImageSubform();

        $firstImageSubform->find('css', 'input[data-test-position]')->setValue($position);
    }

    public function modifyPositionOfImageWithType(string $type, int $position): void
    {
        $this->changeTab();

        $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);

        $imageSubform->find('css', 'input[data-test-position]')->setValue($position);
    }

    public function hasImageWithTypeOnPosition(string $type, int $position): bool
    {
        $this->changeTab();

        $imageSubform = $this->getElement('image_subform_with_type', ['%type%' => $type]);
        $imagePosition = $imageSubform->find('css', 'input[data-test-position]')->getValue();

        return $imagePosition === (string) $position;
    }

    public function selectVariantForFirstImage(ProductVariantInterface $productVariant): void
    {
        $this->changeTab();

        $imageSubform = $this->getFirstImageSubform();
        $this->autocompleteHelper->selectByValue(
            $this->getDriver(),
            $imageSubform->find('css', '[data-test-product-variant]')->getXpath(),
            $productVariant->getCode(),
        );
    }

    public function hasValidationErrorWithMessage(string $message): bool
    {
        $validationMessage = $this->getDocument()->find('css', '.invalid-feedback');

        return $validationMessage !== null && $validationMessage->getText() === $message;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(),
            [
            'add_image' => '[data-test-add-image]',
            'image_subform_with_type' => '[data-test-image-subform][data-test-type="%type%"]',
            'images' => '[data-test-images]',
            'side_navigation_tab' => '[data-test-side-navigation-tab="%name%"]',
        ],
        );
    }

    protected function getFirstImageSubform(): NodeElement
    {
        $images = $this->getElement('images');
        $imageSubforms = $images->findAll('css', '[data-test-image-subform]');

        return reset($imageSubforms);
    }

    protected function changeTab(): void
    {
        if (DriverHelper::isNotJavascript($this->getDriver())) {
            return;
        }

        $this->getElement('side_navigation_tab', ['%name%' => 'media'])->click();
    }
}
