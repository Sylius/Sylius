@ui-cart
Feature: Apply correct simple promotions on order
    In order to pay proper amount while buying promoted goods
    As a Customer
    I want to have promotions applied

    Background:
        Given the store is operating on a single channel
        And default currency is "EUR"
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has a product "Symfony Mug" priced at "€5.00"
        And there is promotion "Special discount"
        And it has "€10.00" "fixed discount" for cart with "item total" above "€80.00"
        And there is promotion "Special discount"
        And it has "10%" "percentage discount" for cart with "item count" above "5"
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Applying fixed discount promotion
        Given I have product "PHP T-Shirt" in the cart
        Then my cart total should be "€90.00"
        And my cart promotions should be "-€10.00"

    Scenario: Applying percentage discount promotion
        When I add 6 products "Symfony Mug" to the cart
        Then my cart total should be "€27.00"
        And my cart promotions should be "-€3.00"

    Scenario: Applying both promotions that order fits
        When I add 6 products "PHP T-Shirt" to the cart
        Then my cart total should be "€531.00"
        And my cart promotions should be "-€69.00"
