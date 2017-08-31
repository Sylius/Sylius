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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Grid
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var array
     */
    private $driverConfiguration;

    /**
     * @var array
     */
    private $sorting = [];

    /**
     * @var array
     */
    private $limits = [];

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var array
     */
    private $actionGroups = [];

    /**
     * @param string $code
     * @param string $driver
     * @param array $driverConfiguration
     */
    private function __construct(string $code, string $driver, array $driverConfiguration)
    {
        $this->code = $code;
        $this->driver = $driver;
        $this->driverConfiguration = $driverConfiguration;
    }

    /**
     * @param string $code
     * @param string $driver
     * @param array $driverConfiguration
     *
     * @return self
     */
    public static function fromCodeAndDriverConfiguration(string $code, string $driver, array $driverConfiguration): self
    {
        return new self($code, $driver, $driverConfiguration);
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return array
     */
    public function getDriverConfiguration(): array
    {
        return $this->driverConfiguration;
    }

    /**
     * @param array $driverConfiguration
     */
    public function setDriverConfiguration(array $driverConfiguration): void
    {
        $this->driverConfiguration = $driverConfiguration;
    }

    /**
     * @return array
     */
    public function getSorting(): array
    {
        return $this->sorting;
    }

    /**
     * @param array $sorting
     */
    public function setSorting(array $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * @return array
     */
    public function getLimits(): array
    {
        return $this->limits;
    }

    /**
     * @param array $limits
     */
    public function setLimits(array $limits): void
    {
        $this->limits = $limits;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getEnabledFields(): array
    {
        return $this->getEnabledItems($this->getFields());
    }

    /**
     * @param Field $field
     *
     * @throws \InvalidArgumentException
     */
    public function addField(Field $field): void
    {
        $name = $field->getName();

        Assert::false($this->hasField($name), sprintf('Field "%s" already exists.', $name));

        $this->fields[$name] = $field;
    }

    /**
     * @param string $name
     */
    public function removeField(string $name): void
    {
        if ($this->hasField($name)) {
            unset($this->fields[$name]);
        }
    }

    /**
     * @param string $name
     *
     * @return Field
     *
     * @throws \InvalidArgumentException
     */
    public function getField(string $name): Field
    {
        Assert::true($this->hasField($name), sprintf('Field "%s" does not exist.', $name));

        return $this->fields[$name];
    }

    /**
     * @param Field $field
     */
    public function setField(Field $field): void
    {
        $name = $field->getName();

        $this->fields[$name] = $field;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField(string $name): bool
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * @return array
     */
    public function getActionGroups(): array
    {
        return $this->actionGroups;
    }

    /**
     * @return array
     */
    public function getEnabledActionGroups(): array
    {
        return $this->getEnabledItems($this->getActionGroups());
    }

    /**
     * @param ActionGroup $actionGroup
     *
     * @throws \InvalidArgumentException
     */
    public function addActionGroup(ActionGroup $actionGroup): void
    {
        $name = $actionGroup->getName();

        Assert::false($this->hasActionGroup($name), sprintf('ActionGroup "%s" already exists.', $name));

        $this->actionGroups[$name] = $actionGroup;
    }

    /**
     * @param string $name
     */
    public function removeActionGroup(string $name): void
    {
        if ($this->hasActionGroup($name)) {
            unset($this->actionGroups[$name]);
        }
    }

    /**
     * @param string $name
     *
     * @return ActionGroup
     */
    public function getActionGroup(string $name): ActionGroup
    {
        Assert::true($this->hasActionGroup($name), sprintf('ActionGroup "%s" does not exist.', $name));

        return $this->actionGroups[$name];
    }

    /**
     * @param ActionGroup $actionGroup
     */
    public function setActionGroup(ActionGroup $actionGroup): void
    {
        $name = $actionGroup->getName();

        $this->actionGroups[$name] = $actionGroup;
    }

    /**
     * @param string $groupName
     *
     * @return Action[]
     */
    public function getActions(string $groupName): array
    {
        return $this->getActionGroup($groupName)->getActions();
    }

    /**
     * @return array
     */
    public function getEnabledActions($groupName): array
    {
        return $this->getEnabledItems($this->getActions($groupName));
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasActionGroup(string $name): bool
    {
        return array_key_exists($name, $this->actionGroups);
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getEnabledFilters(): array
    {
        return $this->getEnabledItems($this->getFilters());
    }

    /**
     * @param Filter $filter
     *
     * @throws \InvalidArgumentException
     */
    public function addFilter(Filter $filter): void
    {
        $name = $filter->getName();

        Assert::false($this->hasFilter($name), sprintf('Filter "%s" already exists.', $name));

        $this->filters[$name] = $filter;
    }

    /**
     * @param string $name
     */
    public function removeFilter(string $name): void
    {
        if ($this->hasFilter($name)) {
            unset($this->filters[$name]);
        }
    }

    /**
     * @param string $name
     *
     * @return Filter
     */
    public function getFilter(string $name): Filter
    {
        Assert::true($this->hasFilter($name), sprintf('Filter "%s" does not exist.', $name));

        return $this->filters[$name];
    }

    /**
     * @param Filter $filter
     */
    public function setFilter(Filter $filter): void
    {
        $name = $filter->getName();

        $this->filters[$name] = $filter;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFilter(string $name): bool
    {
        return array_key_exists($name, $this->filters);
    }

    /**
     * @param array $items
     *
     * @return array
     */
    private function getEnabledItems(array $items): array
    {
        $filteredItems = [];
        foreach ($items as $item) {
            if ($item->isEnabled()) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }
}
