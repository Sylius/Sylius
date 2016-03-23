@shipping
Feature: Prevent deletion of used shipping method
    In order to maintain proper order history
    As an Administrator
    I want to be prevented from deleting used shipping method

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt"
        And the store allows shipping with "DHL Express"
        And the store allows paying with "Cash on Delivery"
        And the customer "john.doe@gmail.com" placed an order "#00000022"
        And the customer chose "DHL Express" shipping method to "France" with "Cash on Delivery" payment
        And the customer bought single "PHP T-Shirt"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Being unable to delete a shipping method which is in use
        When I try to delete "DHL Express" shipping method
        Then I should be notified that it is in use
        And it should not be removed
