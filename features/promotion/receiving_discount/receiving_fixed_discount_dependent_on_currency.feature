@receiving_discount
Feature: Receiving fixed discount dependent on currency on cart
    In order to pay proper amount while buying promoted goods in different than base currency
    As a Visitor
    I want to have promotions applied to my cart

    Background:
        Given the store has currency "USD"
        And the store has currency "GBP" with exchange rate 0.5
        And the store operates on a channel named "Web-US" in "USD" currency
        And that channel allows to shop using "USD" and "GBP" currencies
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"

    @ui @todo
    Scenario: Receiving fixed discount in different than base currency for my cart
        Given this promotion gives "$10.00" in base currency or "£8.00" in "GBP" discount to every order
        When I switch to the "GBP" currency
        And I add product "PHP T-Shirt" to the cart
        Then my cart total should be "£42.00"
        And my discount should be "-£8.00"

    @ui @todo
    Scenario: Receiving fixed discount in different than base currency on a single item
        Given this promotion gives "$10.00" in base currency or "£8.00" in "GBP" off on every product with minimum price at "$20.00"
        When I switch to the "GBP" currency
        And I add product "PHP T-Shirt" to the cart
        Then its price should be decreased by "£8.00"
        And my cart total should be "£42.00"
