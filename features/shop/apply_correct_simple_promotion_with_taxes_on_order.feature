@ui-cart
Feature: Apply correct simple promotions with taxes on order
    In order to pay proper amount while buying promoted taxed goods
    As a Customer
    I want to have promotions and taxes applied

    Background:
        Given the store is operating on a single channel
        And there is "EU" zone containing all members of European Union
        And default currency is "EUR"
        And default tax zone is "EU"
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has a product "Symfony Mug" priced at "€5.00"
        And there is promotion "Special discount"
        And it has "€10.00" "fixed discount" for cart with "item total" above "€80.00"
        And there is promotion "Special discount"
        And it has "10%" "percentage discount" for cart with "item count" above "5"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "Low VAT" tax rate of 5% for "Mugs" within "EU" zone
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And store has a product "Symfony Mug" priced at "€50.00"
        And product "Symfony Mug" belongs to "Mugs" tax category
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Applying fixed discount promotion and taxes
        Given I have product "PHP T-Shirt" in the cart
        Then my cart total should be "€110.70"
        And my cart promotions should be "-€10.00"
        And my cart taxes should be "€20.70"

    Scenario: Applying percentage discount promotion and taxes
        When I add 6 products "Symfony Mug" to the cart
        Then my cart total should be "€28.35"
        And my cart promotions should be "-€3.00"
        And my cart taxes should be "€1.35"

    Scenario: Applying both promotions that order fits and taxes
        When I add 6 products "PHP T-Shirt" to the cart
        Then my cart total should be "€653,13"
        And my cart promotions should be "-€69.00"
        And my cart taxes should be "€122.13"
