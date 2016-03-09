<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Contact\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;

/**
 * Contact topic translation model.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class TopicTranslation extends AbstractTranslation implements TopicTranslationInterface
{
    /**
     * Category id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Category title.
     *
     * @var string
     */
    protected $title;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
