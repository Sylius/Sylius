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
use Sylius\Behat\Behaviour\ChoosesName;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChoosesName;

    /**
     * {@inheritdoc}
     */
    public function addProvince($name, $code, $abbreviation = null)
    {
        $this->getDocument()->clickLink('Add province');

        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Name', $name);
        $provinceForm->fillField('Code', $code);

        if (null !== $abbreviation) {
            $provinceForm->fillField('Abbreviation', $abbreviation);
        }
    }

    /**
     *{@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'provinces' => '#sylius_country_provinces',
        ]);
    }

    /**
     * @return Element
     */
    private function getLastProvinceElement()
    {
        $provinces = $this->getElement('provinces');
        $items = $provinces->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
