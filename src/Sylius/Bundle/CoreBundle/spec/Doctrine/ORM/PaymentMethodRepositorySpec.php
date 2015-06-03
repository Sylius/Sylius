<?php

namespace spec\Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

class PaymentMethodRepositorySpec extends ObjectBehavior
{
    public function let(EntityManager $em, ClassMetadata $classMetadata)
    {
        $this->beConstructedWith($em, $classMetadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentMethodRepository');
    }

    function it_is_a_repository()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository');
        $this->shouldImplement('Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface');
    }

    function it_creates_query_builder_for_the_payment_method(
        $em,
        QueryBuilder $builder,
        ChannelInterface $channel,
        ArrayCollection $paymentMethods,
        PaymentMethodInterface $paymentMethod
    ) {
        $em->createQueryBuilder()->shouldBeCalled()->willReturn($builder);
        $builder->select('method')->shouldBeCalled()->willReturn($builder);
        $builder->from(Argument::any(), 'method')->shouldBeCalled()->willReturn($builder);
        $builder->andWhere('method IN (:methods)')->shouldBeCalled()->willReturn($builder);

        $channel->getPaymentMethods()->shouldBeCalled()->willReturn($paymentMethods);
        $paymentMethods->toArray()->shouldBeCalled()->willReturn(array($paymentMethod));
        $builder->setParameter('methods', array($paymentMethod))->shouldBeCalled()->willReturn($builder);

        $this->getQueryBuidlerForChoiceType(array(
            'channel' => $channel,
            'disabled' => true,
        ))->shouldReturn($builder);
    }
}
