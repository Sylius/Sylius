@managing_promotions
Feature: Increasing a promotion usage after placing an order
    In order to limit promotion usage
    As an Administrator
    I want to have a promotion usage increased after order placement

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$30.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Limited promotion" limited to 5 usages
        And it gives "$10.00" discount to every order
        And I am logged in as an administrator

    @ui
    Scenario: Seeing promotion usage increased after order placement
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse promotions
        Then the promotion "Limited promotion" should be used 1 time
