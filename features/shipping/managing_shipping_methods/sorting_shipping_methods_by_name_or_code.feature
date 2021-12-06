@managing_shipping_methods
Feature: Sorting listed shipping methods by name or code
    In order to change the order by which shipping methods are displayed
    As an Administrator
    I want to sort shipping methods

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store allows shipping with "Aardvark Stagecoach" identified by "ground"
        And this shipping method is named "Zwykły Dyliżans Mrówkojadów" in the "Polish (Poland)" locale
        And the store also allows shipping with "Narwhal Submarine" identified by "marine"
        And this shipping method is named "Łódź Podwodna Morskich Jednorożców" in the "Polish (Poland)" locale
        And the store also allows shipping with "Pug Blimp" identified by "aerial"
        And this shipping method is named "Sterowiec Mopsów" in the "Polish (Poland)" locale
        And I am logged in as an administrator

    @ui @api
    Scenario: Shipping methods can be sorted by code in ascending order
        Given I am browsing shipping methods
        When I sort the shipping methods ascending by code
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have code "aerial"

    @ui @api
    Scenario: Changing the order of sorting by code
        Given I am browsing shipping methods
        When I sort the shipping methods ascending by code
        And I switch the way shipping methods are sorted descending by code
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have code "marine"

    @ui @api
    Scenario: Shipping methods can be sorted by their names
        Given I am browsing shipping methods
        When I sort the shipping methods ascending by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Aardvark Stagecoach"

    @ui @api
    Scenario: Changing the order of sorting shipping methods by their names
        Given I am browsing shipping methods
        When the shipping methods are already sorted ascending by name
        And I switch the way shipping methods are sorted descending by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Pug Blimp"

    @ui @api
    Scenario: Sorting shipping methods ascending by name from chosen locale translation
        When I change my locale to "Polish (Poland)"
        And I browse shipping methods
        And I sort the shipping methods ascending by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Łódź Podwodna Morskich Jednorożców"

    @ui @api
    Scenario: Sorting shipping methods descending by name from chosen locale translation
        When I change my locale to "Polish (Poland)"
        And I browse shipping methods
        And the shipping methods are already sorted ascending by name
        And I sort the shipping methods descending by name
        Then I should see 3 shipping methods in the list
        And the first shipping method on the list should have name "Zwykły Dyliżans Mrówkojadów"
