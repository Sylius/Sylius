@managing_payment_methods
Feature: Prevent deletion of used payment method
    In order to maintain proper order history
    As an Administrator
    I want to be prevented from deleting used payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store allows shipping with "DHL Express"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "DHL Express" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Being unable to delete a payment method which is in use
        When I try to delete the "Cash on Delivery" payment method
        Then I should be notified that it is in use
        And this payment method should still be in the registry
