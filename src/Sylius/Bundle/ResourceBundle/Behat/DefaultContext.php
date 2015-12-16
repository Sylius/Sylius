<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Behat;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class DefaultContext extends RawMinkContext implements Context, KernelAwareContext
{
    /**
     * @var string
     */
    protected $applicationName = 'sylius';

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $actions = array(
        'viewing'  => 'show',
        'creation' => 'create',
        'editing'  => 'update',
        'building' => 'build',
    );

    /**
     * @var KernelInterface
     */
    protected $kernel;

    public function __construct($applicationName = null)
    {
        \Locale::setDefault('en');

        $this->faker = FakerFactory::create();

        if (null !== $applicationName) {
            $this->applicationName = $applicationName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return object
     */
    protected function findOneByName($type, $name)
    {
        return $this->findOneBy($type, array('name' => trim($name)));
    }

    /**
     * @param string $type
     * @param array  $criteria
     *
     * @return object
     *
     * @throws \InvalidArgumentException
     */
    protected function findOneBy($type, array $criteria)
    {
        $resource = $this
            ->getRepository($type)
            ->findOneBy($criteria)
        ;

        if (null === $resource) {
            throw new \InvalidArgumentException(
                sprintf('%s for criteria "%s" was not found.', str_replace('_', ' ', ucfirst($type)), serialize($criteria))
            );
        }

        return $resource;
    }

    /**
     * @param string $resourceName
     *
     * @return RepositoryInterface
     */
    protected function getRepository($resourceName)
    {
        return $this->getService($this->applicationName.'.repository.'.$resourceName);
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getService('doctrine')->getManager();
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Configuration converter.
     *
     * @param string $configurationString
     *
     * @return array
     */
    protected function getConfiguration($configurationString)
    {
        $configuration = array();
        $list = explode(',', $configurationString);

        foreach ($list as $parameter) {
            list($key, $value) = explode(':', $parameter, 2);
            $key = strtolower(trim(str_replace(' ', '_', $key)));

            switch ($key) {
                case 'country':
                    $isoName = $this->getCountryCodeByEnglishCountryName(trim($value));

                    $configuration[$key] = $this->getRepository('country')->findOneBy(array('isoName' => $isoName))->getId();
                    break;

                case 'taxons':
                    $configuration[$key] = new ArrayCollection(array($this->getRepository('taxon')->findOneBy(array('name' => trim($value)))->getId()));
                    break;

                case 'variant':
                    $configuration[$key] = $this->getRepository('product')->findOneBy(array('name' => trim($value)))->getMasterVariant()->getId();
                    break;

                case 'amount':
                    $configuration[$key] = (int) $value;
                    break;

                default:
                    $configuration[$key] = trim($value);
                    break;
            }
        }

        return $configuration;
    }

    /**
     * Generate page url.
     * This method uses simple convention where page argument is prefixed
     * with the application name and used as route name passed to router generate method.
     *
     * @param object|string $page
     * @param array         $parameters
     *
     * @return string
     */
    protected function generatePageUrl($page, array $parameters = array())
    {
        if (is_object($page)) {
            return $this->generateUrl($page, $parameters);
        }

        $route  = str_replace(' ', '_', trim($page));
        $routes = $this->getContainer()->get('router')->getRouteCollection();

        if (null === $routes->get($route)) {
            $route = $this->applicationName.'_'.$route;
        }

        if (null === $routes->get($route)) {
            $route = str_replace($this->applicationName.'_', $this->applicationName.'_backend_', $route);
        }

        $route = str_replace(array_keys($this->actions), array_values($this->actions), $route);
        $route = str_replace(' ', '_', $route);

        return $this->generateUrl($route, $parameters);
    }

    /**
     * Get current user instance.
     *
     * @return UserInterface|null
     *
     * @throws \Exception
     */
    protected function getUser()
    {
        $token = $this->getSecurityContext()->getToken();

        if (null === $token) {
            throw new \Exception('No token found in security context.');
        }

        return $token->getUser();
    }

    /**
     * @return SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
    }

    /**
     * @param string  $route
     * @param array   $parameters
     * @param Boolean $absolute
     *
     * @return string
     */
    protected function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->locatePath($this->getService('router')->generate($route, $parameters, $absolute));
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     *
     * @param string $button
     *
     * @throws ElementNotFoundException
     */
    protected function pressButton($button)
    {
        $this->getSession()->getPage()->pressButton($this->fixStepArgument($button));
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @param string $link
     *
     * @throws ElementNotFoundException
     */
    protected function clickLink($link)
    {
        $this->getSession()->getPage()->clickLink($this->fixStepArgument($link));
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @param string $field
     * @param string $value
     *
     * @throws ElementNotFoundException
     */
    protected function fillField($field, $value)
    {
        $this->getSession()->getPage()->fillField(
            $this->fixStepArgument($field),
            $this->fixStepArgument($value)
        );
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @param string $select
     * @param string $option
     *
     * @throws ElementNotFoundException
     */
    protected function selectOption($select, $option)
    {
        $this->getSession()->getPage()->selectFieldOption(
            $this->fixStepArgument($select),
            $this->fixStepArgument($option)
        );
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

    /**
     * @param NodeElement $table
     * @param string $columnName
     *
     * @return integer
     *
     * @throws \Exception If column was not found
     */
    protected function getColumnIndex(NodeElement $table, $columnName)
    {
        $rows = $table->findAll('css', 'tr');

        if (!isset($rows[0])) {
            throw new \Exception("There are no rows!");
        }

        /** @var NodeElement $firstRow */
        $firstRow = $rows[0];
        $columns = $firstRow->findAll('css', 'th,td');
        foreach ($columns as $index => $column) {
            /** @var NodeElement $column */
            if (0 === stripos($column->getText(), $columnName)) {
                return $index;
            }
        }

        throw new \Exception(sprintf('Column with name "%s" not found!', $columnName));
    }

    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return NodeElement|null
     *
     * @throws \Exception If column was not found
     */
    protected function getRowWithFields(NodeElement $table, array $fields)
    {
        $foundRows = $this->getRowsWithFields($table, $fields, true);

        if (empty($foundRows)) {
            return null;
        }

        return current($foundRows);
    }

    /**
     * @param NodeElement $table
     * @param array $fields
     * @param boolean $onlyFirstOccurence
     *
     * @return NodeElement[]
     *
     * @throws \Exception If columns or rows were not found
     */
    protected function getRowsWithFields(NodeElement $table, array $fields, $onlyFirstOccurence = false)
    {
        $rows = $table->findAll('css', 'tr');

        if (!isset($rows[0])) {
            throw new \Exception("There are no rows!");
        }

        $fields = $this->replaceColumnNamesWithColumnIds($table, $fields);

        $foundRows = array();

        /** @var NodeElement[] $rows */
        $rows = $table->findAll('css', 'tr');
        foreach ($rows as $row) {
            $found = true;

            /** @var NodeElement[] $columns */
            $columns = $row->findAll('css', 'th,td');
            foreach ($fields as $index => $searchedValue) {
                if (!isset($columns[$index])) {
                    throw new \InvalidArgumentException(sprintf('There is no column with index %d', $index));
                }

                if (0 !== stripos(trim($columns[$index]->getText()), trim($searchedValue))) {
                    $found = false;

                    break;
                }
            }

            if ($found) {
                $foundRows[] = $row;

                if ($onlyFirstOccurence) {
                    break;
                }
            }
        }

        return $foundRows;
    }

    /**
     * @param NodeElement $table
     * @param string[] $fields
     *
     * @return string[]
     *
     * @throws \Exception
     */
    protected function replaceColumnNamesWithColumnIds(NodeElement $table, array $fields)
    {
        $replacedFields = array();
        foreach ($fields as $columnName => $expectedValue) {
            $columnIndex = $this->getColumnIndex($table, $columnName);

            $replacedFields[$columnIndex] = $expectedValue;
        }

        return $replacedFields;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not found in country code registry.
     */
    protected function getCountryCodeByEnglishCountryName($name)
    {
        $names = Intl::getRegionBundle()->getCountryNames('en');
        $isoName = array_search(trim($name), $names);

        if (null === $isoName) {
            throw new \InvalidArgumentException(sprintf(
                'Country "%s" not found! Available names: %s.', $name, join(', ', $names)
            ));
        }

        return $isoName;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not found in locale code registry.
     */
    protected function getLocaleCodeByEnglishLocaleName($name)
    {
        $names = Intl::getLocaleBundle()->getLocaleNames('en');
        $code = array_search(trim($name), $names);

        if (null === $code) {
            throw new \InvalidArgumentException(sprintf(
                'Locale "%s" not found! Available names: %s.', $name, join(', ', $names)
            ));
        }

        return $code;
    }
}
