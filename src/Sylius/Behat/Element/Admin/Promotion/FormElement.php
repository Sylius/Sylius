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

namespace Sylius\Behat\Element\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\TabsHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

final class FormElement extends Element implements FormElementInterface
{
    public function prioritizeIt(?int $priority): void
    {
        $this->getElement('priority')->setValue($priority);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'priority' => '#sylius_promotion_priority',
        ]);
    }
}
