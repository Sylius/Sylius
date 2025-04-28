@checkout
Feature: Having only possible country preselected
    In order to not be forced to select country when only one country is available
    As a Visitor
    I want to have only available country preselected

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free

    @no-api @ui
    Scenario: Having the only country preselected on addressing form
        Given I added product "PHP T-Shirt" to the cart
        When I go to the checkout addressing step
        Then I should have "United States" selected as country

    @no-api @ui
    Scenario: Having no country selected if there is more than one country available
        Given the store operates in "United Kingdom"
        And I added product "PHP T-Shirt" to the cart
        When I go to the checkout addressing step
        Then I should have no country selected
