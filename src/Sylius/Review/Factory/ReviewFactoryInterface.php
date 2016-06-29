<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Review\Factory;

use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Review\Model\ReviewableInterface;
use Sylius\Review\Model\ReviewerInterface;
use Sylius\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ReviewFactoryInterface extends FactoryInterface
{
    /**
     * @param ReviewableInterface $subjectId
     *
     * @return ReviewInterface
     */
    public function createForSubject($subjectId);

    /**
     * @param mixed $subjectId
     * @param ReviewerInterface|null $reviewer
     * 
     * @return ReviewInterface
     */
    public function createForSubjectWithReviewer($subjectId, ReviewerInterface $reviewer = null);
}
