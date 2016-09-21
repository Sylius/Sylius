@checkout
Feature: Having only possible country preselected
    In order to not be forced to select country when only one country is available
    As a Customer
    I want to have only available country preselected

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am a logged in customer

    @ui
    Scenario: Having the only country preselected on addressing form
        When I add product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        Then I should have "United States" selected as country

    @ui
    Scenario: Having no country selected if there is more than one country available
        Given the store operates in "United Kingdom"
        When I add product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        Then I should have no country selected
