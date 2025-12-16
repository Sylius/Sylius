@checkout
Feature: Sorting shipping method selection
    In order to see the most suitable shipping methods first
    As a Customer
    I want to have them already sorted

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Targaryen T-Shirt" priced at "$19.99"
        And the store allows shipping with "Aardvark Stagecoach" at position 2
        And the store also allows shipping with "Narwhal Submarine" at position 0
        And the store also allows shipping with "Pug Blimp" at position 1
        And I am a logged in customer

    @api @ui
    Scenario: Seeing shipping methods sorted
        Given I added product "Targaryen T-Shirt" to the cart
        And I addressed the cart
        When I go to the shipping step
        Then I should have "Narwhal Submarine" shipping method available as the first choice
        And I should have "Aardvark Stagecoach" shipping method available as the last choice
