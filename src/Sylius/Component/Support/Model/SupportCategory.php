<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Support\Model;

use Sylius\Component\Translation\Model\AbstractTranslatable;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class SupportCategory extends AbstractTranslatable implements SupportCategoryInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($name)
    {
        $this->translate()->setTitle($name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
