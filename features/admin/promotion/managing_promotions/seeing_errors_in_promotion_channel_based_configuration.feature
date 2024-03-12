@managing_promotions
Feature: Seeing errors in promotion channel based configuration
    In order to quickly find the errors in the form
    As an Administrator
    I want to see the errors count on the channel tabs

    Background:
        Given the store operates on a channel named "Web"
        And the store also operates on a channel named "Mobile"
        And the store has a product "PHP T-Shirt"
        And there is a promotion "Holiday promotion"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing the number of errors per channel in order fixed discount
        Given this promotion gives "$10.00" discount to every order in the "Web" channel and "$5.00" discount to every order in the "Mobile" channel
        When I want to modify a "Holiday promotion" promotion
        And I remove the discount amount for "Mobile" channel
        And I try to save my changes
        And I should see that the action for "Mobile" channel has 1 validation error

    @ui @no-api
    Scenario: Seeing the number of errors per channel in product fixed discount
        Given this promotion gives "$2.00" off on every product in the "Web" channel and "$3.00" off in the "Mobile" channel
        When I want to modify a "Holiday promotion" promotion
        And I remove the discount amount for "Mobile" channel
        And I try to save my changes
        And I should see that the action for "Mobile" channel has 1 validation error

    @ui @no-api
    Scenario: Seeing the number of errors per channel in product percentage discount
        Given this promotion gives "5%" off on every product in the "Web" channel and "10%" off in the "Mobile" channel
        When I want to modify a "Holiday promotion" promotion
        And I remove the discount percentage for "Mobile" channel
        And I try to save my changes
        And I should see that the action for "Mobile" channel has 1 validation error

    @ui @no-api
    Scenario: Seeing the number of errors per channel with rule based on items' total
        Given this promotion gives "$10.00" discount to every order in the "Web" channel and "$5.00" discount to every order in the "Mobile" channel
        And this promotion only applies to orders with a total of at least "$100.00" for "Web" channel and "$50.00" for "Mobile" channel
        When I want to modify a "Holiday promotion" promotion
        And I remove the rule amount for "Mobile" channel
        And I try to save my changes
        And I should see that the rule for "Mobile" channel has 1 validation error
