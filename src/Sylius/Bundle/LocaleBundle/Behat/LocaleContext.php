<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Behat;

use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleContext extends DefaultContext
{
    /**
     * @Given /^there is a disabled locale "([^""]*)"$/
     */
    public function thereIsDisabledLocale($name)
    {
        $this->thereIsLocale($name, false);
    }

    /**
     * @Given /^I created locale "([^""]*)"$/
     * @Given /^there is locale "([^""]*)"$/
     * @Given /^there is an enabled locale "([^""]*)"$/
     */
    public function thereIsLocale($name, $enabled = true, $flush = true)
    {
        $code = $this->getLocaleCodeByEnglishLocaleName($name);

        /* @var $locale LocaleInterface */
        if (null === $locale = $this->getRepository('locale')->findOneBy(['code' => $code])) {
            $locale = $this->getFactory('locale')->createNew();
            $locale->setCode(trim($code));
            $locale->setEnabled($enabled);

            $manager = $this->getEntityManager();
            $manager->persist($locale);
            if ($flush) {
                $manager->flush();
            }
        }

        return $locale;
    }
}
