@managing_promotions
Feature: Decreasing a promotion usage after cancelling an order
    In order to properly limit promotion usage
    As an Administrator
    I want to have a promotion usage decreased after order cancellation

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Limited promotion" limited to 5 usages
        And I am logged in as an administrator

    @ui
    Scenario: Seeing promotion usage decreased after order cancellation
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the customer cancelled this order
        When I browse promotions
        Then the promotion "Limited promotion" should be used 0 time
