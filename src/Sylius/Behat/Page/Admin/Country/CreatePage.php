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

use Sylius\Behat\Behaviour\ChoosesName;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChoosesName;

    /**
     * {@inheritdoc}
     */
    public function fillProvinceNameAndCode($name, $code)
    {
        $this->getDocument()->clickLink('Add province');

        $provinces = $this->getDocument()->find('css', '#sylius_country_provinces');

        $provinces->fillField('sylius_country_provinces_0_name', $name);
        $provinces->fillField('sylius_country_provinces_0_code', $code);
    }
}
