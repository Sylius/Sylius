<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Locale;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function isLocaleDisabled(LocaleInterface $locale)
    {
        return $this->checkLocaleStatus($locale, 'Disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isLocaleEnabled(LocaleInterface $locale)
    {
        return $this->checkLocaleStatus($locale, 'Enabled');
    }

    /**
     * @param LocaleInterface $locale
     * @param string $status
     *
     * @return bool
     */
    private function checkLocaleStatus(LocaleInterface $locale, $status)
    {
        try {
            $tableManipulator = $this->getTableManipulator();
            $table = $this->getElement('table');

            $row = $tableManipulator->getRowWithFields($table, ['code' => $locale->getCode()]);
            $enabledField = $tableManipulator->getFieldFromRow($table, $row, 'Enabled');

            return $enabledField->getText() === $status;
        } catch (\InvalidArgumentException $exception) {
            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }
}
