@inventory
Feature: Validating product quantity on checkout complete
    In order to be sure that products I buy are available
    As a Customer
    I want to have them validated on checkout complete

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Iron Maiden T-Shirt" priced at "€12.54"
        And this product is tracked by the inventory
        And there are 5 items of product "Iron Maiden T-Shirt" available in the inventory
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Product quantity validation passes
        Given I have 3 products "Iron Maiden T-Shirt" in the cart
        And I complete addressing step with email "john@example.com" and "France" as shipping country
        And I select "Free" shipping method
        And I complete the shipping step
        And I choose "Offline" payment method
        When I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Product quantity validation fails
        Given I have 5 products "Iron Maiden T-Shirt" in the cart
        And I complete addressing step with email "john@example.com" and "France" as shipping country
        And I select "Free" shipping method
        And I complete the shipping step
        And I choose "Offline" payment method
        When product "Iron Maiden T-Shirt" quantity is changed to 3
        And I confirm my order
        Then I should not see the thank you page
        And I should be notified that this product does not have sufficient stock
