How to force already registered user to login during checkout in Sylius API?
============================================================================

You can force the user to log in during checkout if he is already registered in your app.

Create a new constraint validator
---------------------------------

Firstly you need to add a new constraint class:

.. code-block:: php

    // src/Validator/Constraints

    <?php

    declare(strict_types=1);

    namespace App\Validator\Constraints;

    use Symfony\Component\Validator\Constraint;

    final class UserAlreadyRegistered extends Constraint
    {
        public string $message = 'This email is already registered. Please log in.';

        public function validatedBy(): string
        {
            return 'sylius_api_registered_user_validator';
        }

        public function getTargets(): string
        {
            return self::CLASS_CONSTRAINT;
        }
    }

Then you need to add a validator class:

.. code-block:: php

    // src/Validator/Constraints

    <?php

    declare(strict_types=1);

    namespace App\Validator\Constraints;

    use Sylius\Component\Core\Model\CustomerInterface;
    use Sylius\Component\Core\Model\ShopUserInterface;
    use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
    use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
    use Symfony\Component\Validator\Constraint;
    use Symfony\Component\Validator\ConstraintValidator;
    use Webmozart\Assert\Assert;

    final class UserAlreadyRegisteredValidator extends ConstraintValidator
    {
        public function __construct(
            private CustomerRepositoryInterface $customerRepository,
            private TokenStorageInterface $tokenStorage
        ) {
        }

        public function validate($value, Constraint $constraint): void
        {
            /** @var UserAlreadyRegistered $constraint */
            Assert::isInstanceOf($constraint, UserAlreadyRegistered::class);

            $token = $this->tokenStorage->getToken();

            /** @var CustomerInterface|null $existingCustomer */
            $existingCustomer = $this->customerRepository->findOneBy(['email' => $value->getEmail()]);
            if (null !== $existingCustomer && !$token->getUser() instanceof ShopUserInterface) {
                $this->context->addViolation($constraint->message);
            }
        }
    }

Enabling your validator
-----------------------

As the last thing you need to do is enable this constraint validator in your app and register it as a service in ``services.yaml``. You can do it this way:

.. code-block:: xml

    // # config/validator/validation.xml

    <?xml version="1.0" encoding="UTF-8"?>

    <constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping http://symfony.com/schema/dic/services/constraint-mapping-1.0.xsd">
        <class name="Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart">
            <constraint name="App\Validator\Constraints\UserAlreadyRegistered">
                <option name="groups">
                    <value>sylius</value>
                </option>
            </constraint>
        </class>
    </constraint-mapping>

.. code-block:: yaml

    // # config/services.yaml

    services:
        # other definitions
        App\Validator\Constraints\UserAlreadyRegisteredValidator:
            class: App\Validator\Constraints\UserAlreadyRegisteredValidator
            tags: [ { name: validator.constraint_validator, alias: sylius_api_registered_user_validator } ]
