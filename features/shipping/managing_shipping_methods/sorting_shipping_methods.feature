@managing_shipping_methods
Feature: Sorting shipping methods on list
    In order to change the order by which shipping methods are displayed
    As an Administrator
    I want to sort shipping methods

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store allows shipping with "Aardvark Stagecoach" identified by "ground"
        And this shipping method is named "Dyliżans Mrówkojadów" in "Polish (Poland)"
        And the store also allows shipping with "Narwhal Submarine" identified by "marine"
        And this shipping method is named "Łódź Podwodna Morskich Jednorożców" in "Polish (Poland)"
        And the store also allows shipping with "Pug Blimp" identified by "aerial"
        And this shipping method is named "Sterowiec Mopsów" in "Polish (Poland)"
        And I am logged in as an administrator

    @ui
    Scenario: Shipping methods are sorted by code in ascending order by default
        When I am browsing shipping methods
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have code "aerial"

    @ui
    Scenario: Changing the order of sorting by code
        Given I am browsing shipping methods
        When I switch the way shipping methods are sorted by code
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have code "marine"

    @ui
    Scenario: Shipping methods can be sorted by their names
        Given I am browsing shipping methods
        When I start sorting shipping methods by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Pug Blimp"

    @ui
    Scenario: Changing the order of sorting shipping methods by their names
        Given I am browsing shipping methods
        And the shipping methods are already sorted by name
        When I switch the way shipping methods are sorted by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Aardvark Stagecoach"

    @ui
    Scenario: Shipping methods are always sorted in the default locale
        Given I change my locale to "Polish (Poland)"
        And I am browsing shipping methods
        When I start sorting shipping methods by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Pug Blimp"
