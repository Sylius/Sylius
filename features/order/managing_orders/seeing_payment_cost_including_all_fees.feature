@managing_orders
Feature: Seeing payment's cost of an order including all fees
    In order to see how much a customer actually has to pay
    As an Administrator
    I want to see the exact payment cost including all additional fees

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Angel T-Shirt" priced at "€39.00"
        And the store has a product "Angel Mug" priced at "€19.00"
        But the store has "DHL" shipping method with "€10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought an "Angel T-Shirt" and an "Angel Mug"
        And the customer chose "DHL" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing total payment
        When I view the summary of the order "#00000666"
        Then it should have 2 items
        And the product named "Angel T-Shirt" should be in the items list
        And the product named "Angel Mug" should be in the items list
        And the order's shipping total should be "€10.00"
        And the order's items total should be "€58.00"
        And the order's total should be "€68.00"
        And the order's payment should also be "€68.00"
