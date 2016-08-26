@managing_promotions
Feature: Prevent deletion of promotions applied to order
    In order to maintain proper order history
    As an Administrator
    I want to be prevented from deleting a promotion which has been applied to an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "PHP Mug" priced at "$12.00"
        And there is a promotion "Christmas sale"
        And it gives "$3.00" discount to every order
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP Mug"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @domain @ui
    Scenario: Being unable to delete a promotion that was applied to an order
        When I try to delete a "Christmas sale" promotion
        Then I should be notified that it is in use and cannot be deleted
        And promotion "Christmas sale" should still exist in the registry
