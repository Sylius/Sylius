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

    @ui
    Scenario: Seeing shipping methods sorted
        Given I have product "Targaryen T-Shirt" in the cart
        When I am at the checkout addressing step
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should have "Narwhal Submarine" shipping method available as the first choice
        And I should have "Aardvark Stagecoach" shipping method available as the last choice
