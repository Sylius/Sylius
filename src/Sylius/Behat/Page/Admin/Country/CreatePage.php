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
     * @var int
     */
    private $provincesCount = 0;

    /**
     * {@inheritdoc}
     */
    public function addProvince($name, $code, $abbreviation = null)
    {
        $this->getDocument()->clickLink('Add province');

        $provinces = $this->getElement('provinces');

        $provinces->fillField('sylius_country_provinces_'.$this->provincesCount.'_name', $name);
        $provinces->fillField('sylius_country_provinces_'.$this->provincesCount.'_code', $code);

        if (null !== $abbreviation) {
            $provinces->fillField('sylius_country_provinces_'.$this->provincesCount.'_abbreviation', $abbreviation);
        }

        $this->provincesCount++;
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
}
