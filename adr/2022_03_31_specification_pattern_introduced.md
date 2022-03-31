# Introduction of Specification Pattern

* Status: proposed
* Date: 2022-03-31

## Context and Problem Statement

During the implementation of Headless Checkout [Pull Request](https://github.com/Sylius/Sylius/pull/13793) we encountered
need to use a Specification pattern as multiple business requirements must be met before completing checkout.

### What Headless Checkout is?
The current checkout system is highly coupled with UI, therefore to maintain the current checkout system, but use it via API
a user had to make at least 5 requests in a specific order (add to cart, address, payment select, shipping select, complete).
The new concept would reduce the number of requests to the API and get rid of the order requirement.

## Decision Drivers

* User must be able to create, change and extend the logic of completing order requirements
* Solution may be generic (abstract) from implementation
* Specification or Composite must be able to be passed via Dependency Injection

## Considered Options

### Option 1 - Tree-Based Builder Specification Pattern
#### Diagram
![](assets/2022_03_31_specification_pattern_introduced/specification_pattern_uml_option_1.png)

#### Implementation Example

```php
abstract class Specification
{
    public abstract function isSatisfiedBy(object $candidate): bool;
    
    public function and(Specification $specification): Specification
    {
        return new AndSpecification($this, $specification);
    }
    
    public function or(Specification $specification): Specification
    {
        return new OrSpecification($this, $specification);
    }
}

final class OrSpecification extends Specification
{
    public function __construct(
        private Specification $left,
        private Specification $right,
    ) {
    }

    public function isSatisfiedBy(object $candidate): bool
    {
        return $this->left->isSatisfiedBy($candidate) || $this->right->isSatisfiedBy($candidate);
    }
}

final class AndSpecification extends Specification
{
    public function __construct(
        private Specification $left,
        private Specification $right,
    ) {
    }

    public function isSatisfiedBy(object $candidate): bool
    {
        return $this->left->isSatisfiedBy($candidate) && $this->right->isSatisfiedBy($candidate);
    }
}

final class Guard
{
    /**
     * @param iterable<Specification> $requirements
     */
    public function __construct(
        private iterable $requirements
    ) {
    }

    public function isSatisfiedBy(OrderInterface $order): bool
    {
        $first = array_shift($this->requirements);

        foreach ($this->requirements as $requirement) {
           $first = $first->and($requirement);
        }

        return $first->isSatisfiedBy($order);
    }
}
```
#### Dependency Injection Example
```xml
<services>
    <service id="sylius.state_guard.state_name" class="Guard">
        <argument type="tagged_iterator" id="sylius.state_guard.state_name.specification" />
    </service>
    
    <service id="sylius.specification.first" class="FirstCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.second" class="SecondCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.third" class="ThirdCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
</services>
```

Same in PHP:
```php
new Guard(
    [ // array or some other iterable
        new FirstCustomSpecification(),
        new SecondCustomSpecification(),
        new ThirdCustomSpecification(),
    ]
);
```

* Good, because it provides `OR` and `AND` abstraction
* Good, because the solution may be used with `tagged_iterator`
* Good, because it may be used in more complex scenarios
* Bad, because Guard does actual voting
* Bad, because it provides Abstract class instead of Interface

### Option 2 - Tree-Based Iterator Specification

```php
interface SpecificationInterface
{
    public function isSatisfiedBy(object $candidate): bool;
}

final class AndSpecification implements SpecificationInterface
{
    /** @var array<int,SpecificationInterface> */
    private array $specifications;

    public function __construct(SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(object $candidate): bool
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($candidate)) {
                return false;
            }
        }

        return true;
    }
}

final class OrSpecification implements SpecificationInterface
{
    /** @var array<int,SpecificationInterface> */
    private array $specifications;

    public function __construct(SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    public function isSatisfiedBy(object $candidate): bool
    {
        foreach ($this->specifications as $specification) {
            if ($specification->isSatisfiedBy($candidate)) {
                return true;
            }
        }

        return false;
    }
}

final class Guard
{
    public function __construct(
        private SpecificationInterface $requirement
    ) {
    }

    public function isSatisfiedBy(OrderInterface $order): bool
    {
        return $this->requirement->isSatisfiedBy($order);
    }
}
```
#### Dependency Injection Example
```xml
<services>
    <service id="sylius.state_guard.state_name" class="Guard">
        <argument type="service" id="sylius.state_guard.state_name.specification" />
    </service>
    
    <service id="sylius.state_guard.state_name.specification" class="AndSpecification">
        <argument type="tagged_iterator" id="sylius.state_guard.state_name.specification" />
    </service>

    <service id="sylius.specification.first" class="FirstCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.second" class="SecondCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.third" class="ThirdCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
</services>
```
Same in PHP:
```php
new Guard(
    new AndSpecification(
        new FirstCustomSpecification(),
        new SecondCustomSpecification(),
        new ThirdCustomSpecification(),
    )
);
```
#### Advanced Dependency Injection Example
```xml
<services>
    <service id="sylius.state_guard.state_name" class="Guard">
        <argument type="service" id="sylius.state_guard.state_name.specification"/>
    </service>
    
    <service id="sylius.state_guard.state_name.specification" class="AndSpecification">
        <argument type="tagged_iterator" id="sylius.state_guard.state_name.specification" />
    </service>
    
    <service id="sylius.state_guard.state_name.partial_specification" class="OrSpecification">
        <argument type="tagged_iterator" id="sylius.state_guard.state_name.or_specification" />
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>

    <service id="sylius.specification.first" class="FirstCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.second" class="SecondCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.third" class="ThirdCustomSpecification">
        <tag name="sylius.state_guard.state_name.or_specification" type="service" />
    </service>
</services>
```
Same in PHP:
```php
new Guard(
    new AndSpecification(
        new FirstCustomSpecification(),
        new SecondCustomSpecification(),
        new OrSpecification(
            new ThirdCustomSpecification(),
        ),
    )
);
```

* Good, because with a factory you can handle pretty complex problem
* Good, because the Guard does not vote
* Good, because it provides `OR` and `AND` abstraction
* Good, because it provides generic interface

### Option 3 - Order-Based Specification

#### Implementation Example

```php
interface SpecificationInterface
{
    public function isSatisfiedBy(object $candidate): bool;
}

final class Guard
{
    /**
     * @param iterable<SpecificationInterface> $requirements
     */
    public function __construct(
        private iterable $requirements
    ) {
    }

    public function isSatisfiedBy(OrderInterface $order): bool
    {
        foreach ($this->requirements as $requirement) {
            if (!$requirement->isSatisfiedBy($order)) {
                return false;
            }
        }

        return true;
    }
}
```
#### Dependency Injection Example
```xml
<services>
    <service id="sylius.state_guard.state_name" class="Guard">
        <argument type="tagged_iterator" id="sylius.state_guard.state_name.specification" />
    </service>
    
    <service id="sylius.specification.first" class="FirstCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.second" class="SecondCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
    <service id="sylius.specification.third" class="ThirdCustomSpecification">
        <tag name="sylius.state_guard.state_name.specification" type="service" />
    </service>
</services>
```

Same in PHP:
```php
new Guard(
    [// array or some other iterable 
        new FirstCustomSpecification(),
        new SecondCustomSpecification(),
        new ThirdCustomSpecification(),
    ]
);
```

* Good, because it is the simplest approach
* Good, because it provides generic interface
* Good, because we can use `tagged_iterator` in a simple way
* Bad, because it lacks `OR` and `AND` abstraction
* Bad, because Guard vote

## Decision Outcome

Before deciding on which option to choose I would like to step back and think about how would User use them.

Let's say the user would like to change the Guard logic. What would be the simplest ways? - To override Guard service.
Let's say the user would like to extend guard logic. What would be the simplest ways? - To create custom Specification,
define it as a service and tag it.

Preserving `tagged_iterator` results with solution being `AND` or `OR` operation only. In that case
**attaching additional Specification will be user-friendly**, but more complex logic will require
overriding/implementing Guard.

Without `tagged_iterator` we could focus on building logic as a composite service and pass the built Specification to
the constructor of the Guard. Building Specification will require some sort of Factory class.

Option 2 is extension of Option 3 with logical operation moved to the `AND` and `OR` Specifications.

Chosen option: **Option 2 - Tree-Based Iterator Specification**
