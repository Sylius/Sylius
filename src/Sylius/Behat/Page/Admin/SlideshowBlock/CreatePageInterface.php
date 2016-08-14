<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\SlideshowBlock;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Vidy Videni <vidy.videni@gmail.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return bool
     */
    public function setPublished();

    /**
     * @param \DateTime $dateTime
     */
    public function setPublishStartDate(\DateTime $dateTime);

    /**
     * @param \DateTime $dateTime
     */
    public function setPublishEndDate(\DateTime $dateTime);

    /**
     * @param \DateTime $publishStartDate
     * @param \DateTime $publishEndDate
     */
    public function addSlide(\DateTime $publishStartDate, \DateTime $publishEndDate);
}
