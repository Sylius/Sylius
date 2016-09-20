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

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->getSession()->getPage()->fillField('Title', $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->getSession()->getPage()->fillField('Internal name', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        $this->getSession()->getPage()->fillField('Body', $body);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'body' => '#sylius_static_content_body',
            'name' => '#sylius_static_content_name',
            'title' => '#sylius_static_content_title',
        ]);
    }
}
