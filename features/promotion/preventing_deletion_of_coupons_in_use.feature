@promotion
Feature: Not being able to delete a coupon which is in use
    In order to maintain proper payment history
    As an Administrator
    I want to be prevented from deleting used coupons

    Background:
        Given the store operates on a single channel in "France"
        And the store ships everywhere for free
        And the store has a product "Jacket"
        And the store has promotion "Christmas sale" with coupon "Santa's gift"
        And the customer "john.doe@gmail.com" placed an order "#00000022"
        And the customer chose "Free" shipping method to "France" with "Cash on Delivery" payment
        And the customer bought single "Jacket" using "Santa's gift" coupon

    @todo
    Scenario: Being unable to delete a used coupon
        When I try to delete "Santa's gift" coupon
        Then And I should be notified that it is in use and cannot be deleted
        And this coupon should still exist in the registry
