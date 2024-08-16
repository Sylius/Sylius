@checkout
Feature: Order shipping method integrity
    In order to have valid shipping method on my order
    As a Visitor
    I want to have valid order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Heavy stuff" shipping category
        And the store has "Small stuff" shipping category
        And the store allows paying Offline
        And the store allows shipping with "DHL"
        And this shipping method requires that all units match to "Heavy stuff" shipping category
        And the store allows shipping with "FedEx"
        And this shipping method requires at least one unit matches to "Small stuff" shipping category
        And the store has a product "Westworld host" priced at "$12.54"
        And this product belongs to "Heavy stuff" shipping category
        And the store has a product "T-Shirt Breaking Bad" priced at "$12.54"
        And this product belongs to "Small stuff" shipping category

    @ui @api
    Scenario: Validate shipping method after administrator changes shipping method requirements
        Given I added product "Westworld host" to the cart
        And I have completed addressing step with email "guest@example.com" and "United States" based billing address
        And I have proceeded order with "DHL" shipping method and "Offline" payment
        And product "Westworld host" shipping category has been changed to "Small stuff"
        When I want to complete checkout
        Then I should not be able to confirm order because products do not fit "DHL" requirements
        And I should not see the thank you page
