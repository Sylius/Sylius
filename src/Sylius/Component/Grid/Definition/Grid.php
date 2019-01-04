<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Definition;

use Webmozart\Assert\Assert;

class Grid
{
    /** @var string */
    private $code;

    /** @var string */
    private $driver;

    /** @var array */
    private $driverConfiguration;

    /** @var array */
    private $sorting = [];

    /** @var array */
    private $limits = [];

    /** @var array */
    private $fields = [];

    /** @var array */
    private $filters = [];

    /** @var array */
    private $actionGroups = [];

    private function __construct(string $code, string $driver, array $driverConfiguration)
    {
        $this->code = $code;
        $this->driver = $driver;
        $this->driverConfiguration = $driverConfiguration;
    }

    public static function fromCodeAndDriverConfiguration(string $code, string $driver, array $driverConfiguration): self
    {
        return new self($code, $driver, $driverConfiguration);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getDriverConfiguration(): array
    {
        return $this->driverConfiguration;
    }

    public function setDriverConfiguration(array $driverConfiguration): void
    {
        $this->driverConfiguration = $driverConfiguration;
    }

    public function getSorting(): array
    {
        return $this->sorting;
    }

    public function setSorting(array $sorting): void
    {
        $this->sorting = $sorting;
    }

    public function getLimits(): array
    {
        return $this->limits;
    }

    public function setLimits(array $limits): void
    {
        $this->limits = $limits;
    }

    /**
     * @return array|Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array|Field[]
     */
    public function getEnabledFields(): array
    {
        return array_filter($this->getFields(), function (Field $field): bool {
            return $field->isEnabled();
        });
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addField(Field $field): void
    {
        $name = $field->getName();

        Assert::false($this->hasField($name), sprintf('Field "%s" already exists.', $name));

        $this->fields[$name] = $field;
    }

    public function removeField(string $name): void
    {
        if ($this->hasField($name)) {
            unset($this->fields[$name]);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getField(string $name): Field
    {
        Assert::true($this->hasField($name), sprintf('Field "%s" does not exist.', $name));

        return $this->fields[$name];
    }

    public function setField(Field $field): void
    {
        $name = $field->getName();

        $this->fields[$name] = $field;
    }

    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * @return array|ActionGroup[]
     */
    public function getActionGroups(): array
    {
        return $this->actionGroups;
    }

    /**
     * @return array|ActionGroup[]
     */
    public function getEnabledActionGroups(): array
    {
        return array_filter($this->getActionGroups(), function (ActionGroup $actionGroup): bool {
            // TODO: There's no `isEnabled` method on ActionGroup, so we assume all of them are enabled
            return true;
        });
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addActionGroup(ActionGroup $actionGroup): void
    {
        $name = $actionGroup->getName();

        Assert::false($this->hasActionGroup($name), sprintf('ActionGroup "%s" already exists.', $name));

        $this->actionGroups[$name] = $actionGroup;
    }

    public function removeActionGroup(string $name): void
    {
        if ($this->hasActionGroup($name)) {
            unset($this->actionGroups[$name]);
        }
    }

    public function getActionGroup(string $name): ActionGroup
    {
        Assert::true($this->hasActionGroup($name), sprintf('ActionGroup "%s" does not exist.', $name));

        return $this->actionGroups[$name];
    }

    public function setActionGroup(ActionGroup $actionGroup): void
    {
        $name = $actionGroup->getName();

        $this->actionGroups[$name] = $actionGroup;
    }

    /**
     * @return array|Action[]
     */
    public function getActions(string $groupName): array
    {
        return $this->getActionGroup($groupName)->getActions();
    }

    /**
     * @return array|Action[]
     */
    public function getEnabledActions($groupName): array
    {
        return array_filter($this->getActions($groupName), function (Action $action): bool {
            return $action->isEnabled();
        });
    }

    public function hasActionGroup(string $name): bool
    {
        return array_key_exists($name, $this->actionGroups);
    }

    /**
     * @return array|Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array|Filter[]
     */
    public function getEnabledFilters(): array
    {
        return array_filter($this->getFilters(), function (Filter $filter): bool {
            return $filter->isEnabled();
        });
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function addFilter(Filter $filter): void
    {
        $name = $filter->getName();

        Assert::false($this->hasFilter($name), sprintf('Filter "%s" already exists.', $name));

        $this->filters[$name] = $filter;
    }

    public function removeFilter(string $name): void
    {
        if ($this->hasFilter($name)) {
            unset($this->filters[$name]);
        }
    }

    public function getFilter(string $name): Filter
    {
        Assert::true($this->hasFilter($name), sprintf('Filter "%s" does not exist.', $name));

        return $this->filters[$name];
    }

    public function setFilter(Filter $filter): void
    {
        $name = $filter->getName();

        $this->filters[$name] = $filter;
    }

    public function hasFilter(string $name): bool
    {
        return array_key_exists($name, $this->filters);
    }
}
