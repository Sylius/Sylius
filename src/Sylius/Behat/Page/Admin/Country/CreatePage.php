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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\Element;
use Sylius\Behat\Behaviour\ChoosesName;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChoosesName;

    /**
     * @var array
     */
    protected $elements = [
        'provinces' => '#sylius_country_provinces',
    ];

    /**
     * If the scenario is using a "@javascript" tag we are selecting Country name differently
     *
     * @param string $name
     */
    public function chooseNameProperly($name)
    {
        if ($this->getSession()->getDriver() instanceof Selenium2Driver) {
            $this->chooseNameWithJavascript($name);

            return;
        }

        $this->chooseName($name);
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
     * @param string $name
     */
    private function chooseNameWithJavascript($name)
    {
        $selectElements = $this->getDocument()->find('css', '.selection');
        $selectElements->click();

        $selectElement = $selectElements->find('css', '.item:contains("'.$name.'")');
        $selectElement->click();
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
