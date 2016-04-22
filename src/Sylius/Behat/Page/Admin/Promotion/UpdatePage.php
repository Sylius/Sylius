<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use NamesIt;
    use ChecksCodeImmutability;

    /**
     * {@inheritdoc}
     */
    public function checkChannelsState($channelName)
    {
        $field = $this->getDocument()->findField($channelName);

        return (bool) $field->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function fillUsageLimit($limit)
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function makeExclusive()
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkCouponBased()
    {
        $this->getDocument()->checkField('Coupon based');
    }

    public function checkChannel($name)
    {
        $this->getDocument()->checkField($name);
    }

    /**
     * {@inheritDoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return [
            'code' => '#sylius_promotion_code',
            'coupon_based' => '#sylius_promotion_couponBased',
            'exclusive' => '#sylius_promotion_exclusive',
            'name' => '#sylius_promotion_name',
            'usage_limit' => '#sylius_promotion_usageLimit',
        ];
    }
}
