@todo
@ui-cart
Feature: Apply fixed discount promotion on order
    In order to pay proper amount while buying promoted goods
    As a Customer
    I want to have promotions applied

    Background:
        Given the store is operating on a single channel
        And default currency is "EUR"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And the store has a product "PHP Mug" priced at "€6.00"
        And the store has a product "Symfony Mug" priced at "€10.00"
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Applying proper promotion discount
        Given there is a promotion "Total based promotion"
        And it gives "€10.00" fixed discount for customers with carts above "€80.00"
        And I have product "PHP T-Shirt" in the cart
        Then my cart total should be "€90.00"
        And my cart promotions should be "-€10.00"

    Scenario: Reset cart total if promotion discount is equal with items total
        Given there is a promotion "Item based promotion"
        And it gives "€16.00" fixed discount for customers with carts with more than 1 item
        And I have product "PHP Mug" in the cart
        And I have product "Symfony Mug" in the cart
        Then my cart total should be "€0.00"
        And my cart promotions should be "-€16.00"

    Scenario: Set cart total to €0.00 if promotion discount is bigger than items total
        Given there is a promotion "Silly promotion"
        And it gives "€10.00" fixed discount for customers with carts above "€5.00"
        And I have product "PHP Mug" in the cart
        Then my cart total should be "€0.00"
        And my cart promotions should be "-€5.00"
