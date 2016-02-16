@cart
Feature: Apply fixed discount promotion on order
    In order to pay proper amount while buying promoted goods
    As a Customer
    I want to have promotions applied to my cart

    Background:
        Given the store is operating on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And the store has a product "PHP Mug" priced at "€6.00"

    @todo
    Scenario: Applying proper promotion discount
        Given there is a promotion "Holiday promotion"
        And it gives "€10.00" fixed discount to every order
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€90.00"
        And my cart promotions should be "-€10.00"

    @todo
    Scenario: Apply proper discount when promotion discount is equal to items total
        Given there is a promotion "Christmas Sale"
        And it gives "€106.00" fixed discount to every order
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then my cart total should be "€0.00"
        And my cart promotions should be "-€106.00"

    @todo
    Scenario: Apply discount equal to items total when promotion discount is bigger than items total
        Given there is a promotion "Thanksgiving sale"
        And it gives "200.00" fixed discount to every order
        When I add product "PHP Mug" to the cart
        Then my cart total should be "€0.00"
        And my cart promotions should be "-€100.00"

    @todo
    Scenario: The fixed discount does not affect a shipping fee
        Given the store has "DHL" shipping method with "€10.00" fee
        And there is a promotion "Holiday promotion"
        And it gives "€10.00" fixed discount to every order
        And I am logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "€100.00"
        And my cart shipping fee should be "€10.00"
        And my cart promotions should be "-€10.00"
