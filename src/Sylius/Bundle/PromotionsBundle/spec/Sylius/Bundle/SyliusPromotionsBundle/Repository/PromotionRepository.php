<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Repository;

use PHPSpec2\ObjectBehavior;

/**
 * Promotion repository spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionRepository extends ObjectBehavior
{
    /**
     * @param Doctrine\ORM\EntityManager         $em
     * @param Doctrine\ORM\Mapping\ClassMetadata $class
     * @param Doctrine\ORM\QueryBuilder          $queryBuilder
     */
    function let($em, $class, $queryBuilder)
    {
        $em
            ->createQueryBuilder()
            ->willReturn($queryBuilder)
        ;

        $queryBuilder
            ->select(ANY_ARGUMENT)
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->from(ANY_ARGUMENTS)
            ->willReturn($queryBuilder)
        ;

        $this->beConstructedWith($em, $class);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Repository\PromotionRepository');
    }

    function it_should_be_Sylius_promotion()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Repository\PromotionRepositoryInterface');
    }

    /**
     * @param Doctrine\ORM\Query\Expr $expr
     */
    function it_should_find_all_active_promotions($expr, $queryBuilder)
    {
        $queryBuilder->expr()->willReturn($expr);

        $expr->lt('o.startsAt', ANY_ARGUMENT)->shouldBeCalled();
        $expr->gt('o.endsAt', ANY_ARGUMENT)->shouldBeCalled();

        $this->findActive();
    }
}
