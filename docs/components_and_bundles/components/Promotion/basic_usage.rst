Basic Usage
===========

In order to benefit from the component's features at first you need to create a basic class that will implement
the :ref:`component_promotion_model_promotion-subject-interface`. Let's assume that you would like to
have a system that applies promotions on Tickets. Your **Ticket** class therefore will implement the
:ref:`component_promotion_model_promotion-countable-subject-interface` to give you an ability to count the subjects
for promotion application purposes.

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Doctrine\Common\Collections\Collection;
    use Doctrine\Common\Collections\ArrayCollection;
    use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
    use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
    use Sylius\Component\Promotion\Model\PromotionInterface;

    class Ticket implements CountablePromotionSubjectInterface
    {
        /**
         * @var int
         */
        private $quantity;

        /**
         * @var Collection
         */
        private $promotions;

        /**
         * @var int
         */
        private $unitPrice;

        public function __construct()
        {
            $this->promotions = new ArrayCollection();
        }
        /**
         * @return int
         */
        public function getQuantity()
        {
            return $this->quantity;
        }

        /**
         * @param int $quantity
         */
        public function setQuantity($quantity)
        {
            $this->quantity = $quantity;
        }

        /**
         * {@inheritdoc}
         */
        public function getPromotions()
        {
            return $this->promotions;
        }

        /**
         * {@inheritdoc}
         */
        public function hasPromotion(PromotionInterface $promotion)
        {
            return $this->promotions->contains($promotion);
        }

        /**
         * {@inheritdoc}
         */
        public function getPromotionSubjectTotal()
        {
            //implementation
        }

        /**
         * {@inheritdoc}
         */
        public function addPromotion(PromotionInterface $promotion)
        {
            if (!$this->hasPromotion($promotion)) {
                $this->promotions->add($promotion);
            }
        }

        /**
         * {@inheritdoc}
         */
        public function removePromotion(PromotionInterface $promotion)
        {
            if($this->hasPromotion($promotion))
            {
                $this->promotions->removeElement($promotion);
            }
        }

        /**
         * {@inheritdoc}
         */
        public function getPromotionSubjectCount()
        {
            return $this->getQuantity();
        }

        /**
         * @return int
         */
        public function getUnitPrice()
        {
            return $this->unitPrice;
        }

        /**
         * @param int $price
         */
        public function setUnitPrice($price)
        {
            $this->unitPrice = $price;
        }

        /**
         * @return int
         */
        public function getTotal()
        {
            return $this->getUnitPrice() * $this->getQuantity();
        }
    }

.. _component_promotion_processor_promotion-processor:

PromotionProcessor
------------------

The component provides us with a **PromotionProcessor** which checks all rules of a subject
and applies configured actions if rules are eligible.


.. code-block:: php

    <?php

    use Sylius\Component\Promotion\Processor\PromotionProcessor;
    use AppBundle\Entity\Ticket;

    /**
     * @param PromotionRepositoryInterface         $repository
     * @param PromotionEligibilityCheckerInterface $checker
     * @param PromotionApplicatorInterface         $applicator
     */
    $processor = new PromotionProcessor($repository, $checker, $applicator);

    $subject = new Ticket();

    $processor->process($subject);

.. note::

    It implements the :ref:`component_promotion_processor_promotion-processor-interface`.

CompositePromotionEligibilityChecker
------------------------------------

The Promotion component provides us with a delegating service - the **CompositePromotionEligibilityChecker** that checks if the promotion rules are eligible for a given subject.
Below you can see how it works:

.. warning::

    Remember! That before you start using rule checkers you need to have two Registries - rule checker registry and promotion action registry.
    In these you have to register your rule checkers and promotion actions. You will also need working services - 'item_count' rule checker service for our example:

.. code-block:: php

    <?php

    use Sylius\Component\Promotion\Model\Promotion;
    use Sylius\Component\Promotion\Model\PromotionAction;
    use Sylius\Component\Promotion\Model\PromotionRule;
    use Sylius\Component\Promotion\Checker\CompositePromotionEligibilityChecker;
    use AppBundle\Entity\Ticket;

    $checkerRegistry = new ServiceRegistry('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    $actionRegistry = new ServiceRegistry('Sylius\Component\Promotion\Model\PromotionActionInterface');
    $ruleRegistry = new ServiceRegistry('Sylius\Component\Promotion\Model\PromotionRuleInterface');

    $dispatcher = new EventDispatcher();

    /**
     * @param ServiceRegistryInterface $registry
     * @param EventDispatcherInterface $dispatcher
     */
    $checker = new CompositePromotionEligibilityChecker($checkerRegistry, $dispatcher);

    $itemCountChecker = new ItemCountRuleChecker();
    $checkerRegistry->register('item_count', $itemCountChecker);

    // Let's create a new promotion
    $promotion = new Promotion();
    $promotion->setName('Test');

    // And a new action for that promotion, that will give a fixed discount of 10
    $action = new PromotionAction();
    $action->setType('fixed_discount');
    $action->setConfiguration(array('amount' => 10));
    $action->setPromotion($promotion);

    $actionRegistry->register('fixed_discount', $action);

    // That promotion will also have a rule - works for item amounts over 2
    $rule = new PromotionRule();
    $rule->setType('item_count');

    $configuration = array('count' => 2);
    $rule->setConfiguration($configuration);

    $ruleRegistry->register('item_count', $rule);

    $promotion->addRule($rule);

    // Now we need an object that implements the PromotionSubjectInterface
    // so we will use our custom Ticket class.
    $subject = new Ticket();

    $subject->addPromotion($promotion);
    $subject->setQuantity(3);
    $subject->setUnitPrice(10);

    $checker->isEligible($subject, $promotion); // Returns true

.. note::

    It implements the :ref:`component_promotion_checker_promotion-eligibility-checker-interface`.

.. _component_promotion_action_promotion-applicator:

PromotionApplicator
-------------------

In order to automate the process of promotion application the component provides us with a Promotion Applicator,
which is able to apply and revert single promotions on a subject implementing the **PromotionSubjectInterface**.

.. code-block:: php

    <?php

    use Sylius\Component\Promotion\PromotionAction\PromotionApplicator;
    use Sylius\Component\Promotion\Model\Promotion;
    use Sylius\Component\Registry\ServiceRegistry;
    use AppBundle\Entity\Ticket;

    // In order for the applicator to work properly you need to have your actions created and registered before.
    $registry = new ServiceRegistry('Sylius\Component\Promotion\Model\PromotionActionInterface');
    $promotionApplicator = new PromotionApplicator($registry);

    $promotion = new Promotion();

    $subject = new Ticket();
    $subject->addPromotion($promotion);

    $promotionApplicator->apply($subject, $promotion);

    $promotionApplicator->revert($subject, $promotion);

.. note::

    It implements the :ref:`component_promotion_action_promotion-applicator-interface`.

.. _component_promotion_generator_coupon-generator:

PromotionCouponGenerator
------------------------

In order to automate the process of coupon generation the component provides us with a Coupon Generator.

.. code-block:: php

    <?php

    use Sylius\Component\Promotion\Model\Promotion;
    use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
    use Sylius\Component\Promotion\Generator\PromotionCouponGenerator;

    $promotion = new Promotion();

    $instruction = new PromotionCouponGeneratorInstruction(); // $amount = 5 by default

    /**
     * @param RepositoryInterface    $repository
     * @param EntityManagerInterface $manager
     */
    $generator = new PromotionCouponGenerator($repository, $manager);

    //This will generate and persist 5 coupons into the database
    //basing on the instruction provided for the given promotion object
    $generator->generate($promotion, $instruction);

    // We can also generate one unique code, and assign it to a new Coupon.
    $code = $generator->generateUniqueCode();
    $coupon = new Coupon();
    $coupon->setCode($code);
