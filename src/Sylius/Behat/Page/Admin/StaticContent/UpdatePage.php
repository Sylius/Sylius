<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\StaticContent;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function changeBodyTo($body)
    {
        $this->getElement('body')->setValue($body);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->getElement('body')->getValue();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'body' => '#sylius_static_content_body',
        ]);
    }

}
