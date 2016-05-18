<?php
/**
 * Created by PhpStorm.
 * User: loic
 * Date: 18/05/2016
 * Time: 11:54
 */

namespace Sylius\Bundle\LikeBundle\Updater;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Like\Model\DislikableInterface;
use Sylius\Component\Like\Model\LikableInterface;
use Sylius\Component\Review\Calculator\LikeCountCalculatorInterface;


/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class LikeCountUpdater
{
    /**
     * @var LikeCountCalculatorInterface
     */
    private $likeCountCalculator;

    /**
     * @var ObjectManager
     */
    private $likeSubjectManager;

    /**
     * RecalculateLikeCountListener constructor.
     *
     * @param LikeCountCalculatorInterface $likeCountCalculator
     * @param ObjectManager $likeSubjectManager
     */
    public function __construct(LikeCountCalculatorInterface $likeCountCalculator, ObjectManager $likeSubjectManager)
    {
        $this->likeCountCalculator = $likeCountCalculator;
        $this->likeSubjectManager = $likeSubjectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function update(LikableInterface $likeSubject)
    {
        $likeSubject
            ->setLikeCount($this->likeCountCalculator->calculateLikeCount($likeSubject));

        if ($likeSubject instanceof DislikableInterface) {
            $likeSubject
                ->setDislikeCount($this->likeCountCalculator->calculateDislikeCount($likeSubject));
        }

        $this->likeSubjectManager->flush();
    }
}
