@managing_promotions
Feature: Not increasing a promotion usage with disabled track usage after placing an order
    In order to allow unlimited promotion usage tracking
    As an Administrator
    I want to have a promotion usage not increased after order placement when track usage is disabled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$30.00"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Limited promotion" limited to 5 usages
        And this promotion has track usage disabled
        And it gives "$10.00" discount to every order
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing promotion usage not increased after order placement when track usage is disabled
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse promotions
        Then the promotion "Limited promotion" should be used 0 time
