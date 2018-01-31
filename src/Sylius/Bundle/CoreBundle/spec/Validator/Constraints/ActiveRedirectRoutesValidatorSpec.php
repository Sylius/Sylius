<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 31/01/18
 * Time: 16:24
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ActiveRedirectRoutes;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ActiveRedirectRoutesValidatorSpec extends ObjectBehavior
{
    /** @var ExecutionContextInterface */
    private $context;

    /** @var ActiveRedirectRoutes */
    private $constraint;

    public function __construct()
    {
        $this->constraint          = new ActiveRedirectRoutes();
        $this->constraint->message = 'Invalid route';
    }

    public function let(URLRedirectRepositoryInterface $repository, ExecutionContextInterface $executionContext)
    {
        $this->beConstructedWith($repository);
        $this->initialize($executionContext);
        $this->context = $executionContext;
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    public function it_does_not_validate_any_object()
    {
        $this->shouldThrow(\TypeError::class)->during('validate', ['string', $this->constraint]);
        $this->shouldThrow(\TypeError::class)->during('validate', [1, $this->constraint]);
        $this->shouldThrow(\TypeError::class)->during('validate', [new MoneyType(), $this->constraint]);
    }

    public function it_does_not_fail_on_inactive_routes(
        URLRedirectRepositoryInterface $repository,
        URLRedirectInterface $URLRedirect
    ) {
        $URLRedirect->isEnabled()->shouldBeCalled()->willReturn(false);

        $URLRedirect->getOldRoute()->shouldNotBeCalled();
        $repository->getActiveRedirectForRoute('/abc')->shouldNotBeCalled();

        $this->context->addViolation($this->constraint->message)->shouldNotBeCalled();

        $this->validate($URLRedirect, $this->constraint);
    }

    public function it_validates_urls_that_do_not_have_conflicts(
        URLRedirectRepositoryInterface $repository,
        URLRedirectInterface $URLRedirect
    ) {
        $URLRedirect->isEnabled()->shouldBeCalled()->willReturn(true);
        $URLRedirect->getOldRoute()->shouldBeCalled()->willReturn('/abc');

        $repository->getActiveRedirectForRoute('/abc')
            ->shouldBeCalled()
            ->willReturn(null);

        $this->context->addViolation($this->constraint->message)->shouldNotBeCalled();

        $this->validate($URLRedirect, $this->constraint);
    }

    public function it_validates_urls_that_have_a_conflict(
        URLRedirectRepositoryInterface $repository,
        URLRedirectInterface $URLRedirect
    ) {
        $URLRedirect->isEnabled()->shouldBeCalled()->willReturn(true);
        $URLRedirect->getOldRoute()->shouldBeCalled()->willReturn('/abc');

        $repository->getActiveRedirectForRoute('/abc')
            ->shouldBeCalled()
            ->willReturn(new URLRedirect('/abc', '/cde'));

        $this->context->addViolation($this->constraint->message)->shouldBeCalled();

        $this->validate($URLRedirect, $this->constraint);

    }
}
