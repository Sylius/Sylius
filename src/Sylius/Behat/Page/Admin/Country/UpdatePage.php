<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Country;

use Behat\Mink\Element\Element;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
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

        return $provinces->has('css', '[value = "'.$provinceName.'"]');
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
            'enabled' => '#sylius_country_enabled',
            'code' => '#sylius_country_code',
            'provinces' => '#sylius_country_provinces',
        ]);
    }

    /**
     * @param string $provinceName
     */
    public function removeProvince($provinceName)
    {
        if ($this->isThereProvince($provinceName)) {
            $provinces = $this->getElement('provinces');

            $item = $provinces->find('css', 'div[data-form-collection="item"] input[value="'.$provinceName.'"]')->getParent()->getParent();
            $item->clickLink('Delete');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fillProvinceData($name, $code, $abbreviation = null)
    {
        $this->getDocument()->clickLink('Add province');

        $provinces = $this->getElement('provinces');

        $provinceForm = $this->getLastProvinceElement();
        $provincesCount = $provinceForm->getAttribute('data-form-collection-index');

        $provinces->fillField('sylius_country_provinces_'.$provincesCount.'_name', $name);
        $provinces->fillField('sylius_country_provinces_'.$provincesCount.'_code', $code);

        if (null !== $abbreviation) {
            $provinces->fillField('sylius_country_provinces_'.$provincesCount.'_abbreviation', $abbreviation);
        }
    }

    /**
     * @return Element
     */
    private function getLastProvinceElement()
    {
        $provinces = $this->getElement('provinces');
        $items = $provinces->findAll('css', 'div[data-form-collection="item"]');

        return end($items);
    }
}
