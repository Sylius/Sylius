@managing_promotions
Feature: Checking a promotion usage after placing an order
    In order to checking promotion usage
    As an Administrator
    I want to have a promotion usage increased or unchanged after order placement

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$20.00"
        And it belongs to "Mugs"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Christmas promotion"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing item fixed discount promotion usage unchanged after order placement
        Given the promotion gives "$10.00" off on every product with minimum price at "$50.00"
        And the promotion gives another "$5.00" off on every product classified as "T-Shirts"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP Mug"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse promotions
        Then the promotion "Christmas promotion" should not be used

    @ui
    Scenario: Seeing item fixed discount promotion usage increased after order placement
        Given the promotion gives "$10.00" off on every product with minimum price at "$50.00"
        And the promotion gives another "$5.00" off on every product classified as "Mugs"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP Mug"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse promotions
        Then the promotion "Christmas promotion" should be used 1 time

    @ui
    Scenario: Seeing shipping percentage discount promotion usage unchanged after order placement
        Given the promotion gives "100%" discount on shipping to every order
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP Mug"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse promotions
        Then the promotion "Christmas promotion" should not be used
