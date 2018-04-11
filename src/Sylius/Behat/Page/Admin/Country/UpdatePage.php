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

namespace Sylius\Behat\Page\Admin\Country;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use Toggles;

    /**
     * {@inheritdoc}
     */
    public function isCodeFieldDisabled()
    {
        $codeField = $this->getElement('code');

        return $codeField->getAttribute('disabled') === 'disabled';
    }

    /**
     * {@inheritdoc}
     */
    public function isThereProvince($provinceName)
    {
        $provinces = $this->getElement('provinces');

        return $provinces->has('css', '[value = "' . $provinceName . '"]');
    }

    /**
     * {@inheritdoc}
     */
    public function isThereProvinceWithCode($provinceCode)
    {
        $provinces = $this->getElement('provinces');

        return $provinces->has('css', '[value = "' . $provinceCode . '"]');
    }

    /** {@inheritdoc} */
    public function hasPostCode(string $postcodeName): bool
    {
        $postcodes = $this->getElement('postcode');

        return $postcodes->has('xpath', '//input[@value="' . $postcodeName . '" and contains(@id, "name")]');
    }

    /**
     * @param string $provinceName
     */
    public function removeProvince($provinceName)
    {
        if ($this->isThereProvince($provinceName)) {
            $provinces = $this->getElement('provinces');

            $item = $provinces
                ->find('css', sprintf('div[data-form-collection="item"] input[value="%s"]', $provinceName))
                ->getParent()
                ->getParent()
                ->getParent()
                ->getParent()
                ->getParent()
            ;

            $item->clickLink('Delete');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addProvince($name, $code, $abbreviation = null)
    {
        $this->clickAddProvinceButton();

        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Name', $name);
        $provinceForm->fillField('Code', $code);

        if (null !== $abbreviation) {
            $provinceForm->fillField('Abbreviation', $abbreviation);
        }
    }

    /** {@inheritdoc} */
    public function addPostCode(string $postcode, string $postcodeName): void
    {
        $this->clickAddPostCodeButton();

        $postcodeForm = $this->getLastPostCodeElement();
        $postcodeForm->fillField('Post code', $postcode);
        $postcodeForm->fillField('Name', $postcodeName);
    }

    /**
     * {@inheritdoc}
     */
    public function clickAddProvinceButton()
    {
        $this->getDocument()->clickLink('Add province');
    }

    /** {@inheritdoc} */
    public function clickAddPostCodeButton()
    {
        $this->getDocument()->clickLink('Add postcode');
    }

    /**
     * {@inheritdoc}
     */
    public function nameProvince($name)
    {
        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function namePostCode($name)
    {
        $postcodeForm = $this->getLastPostCodeElement();

        $postcodeForm->fillField('Name', $name);
    }

    /**
     * @param string $provinceName
     */
    public function removeProvinceName($provinceName)
    {
        if ($this->isThereProvince($provinceName)) {
            $provinces = $this->getElement('provinces');

            $item = $provinces->find('css', '[value="' . $provinceName . '"]')->getParent();
            $item->fillField('Name', '');
        }
    }

    /**
     * @throws ElementNotFoundException
     */
    public function removePostCode(string $postcodeName): void
    {
        if (!$this->hasPostCode($postcodeName)) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '[value="' . $postcodeName . '"]');
        }

        $provinces = $this->getElement('postcode');

        $item = $provinces->find('css', '[value="' . $postcodeName . '"]')->getParent();
        $item->fillField('Name', '');
    }

    /**
     * {@inheritdoc}
     */
    public function specifyProvinceCode($code)
    {
        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Code', $code);
    }

    /** {@inheritdoc} */
    public function specifyPostCodeValue($postcodeValue)
    {
        $postcodeForm = $this->getLastPostCodeElement();
        $postcodeForm->fillField('Post code', $postcodeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessage($element)
    {
        $page = $this->getSession()->getPage();

        $foundElement = $page->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getToggleableElement()
    {
        return $this->getElement('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_country_code',
            'enabled' => '#sylius_country_enabled',
            'postcode' => '#sylius_country_postcodes',
            'provinces' => '#sylius_country_provinces',
        ]);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getLastProvinceElement(): NodeElement
    {
        $provinces = $this->getElement('provinces');
        $items = $provinces->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }

    private function getLastPostCodeElement(): NodeElement
    {
        $postcodes = $this->getElement('postcode');
        $items     = $postcodes->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
