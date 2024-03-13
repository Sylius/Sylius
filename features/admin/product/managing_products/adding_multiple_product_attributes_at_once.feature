@managing_products
Feature: Adding a new integer product attribute
    In order to manage the product attributes easily
    As an Administrator
    I want to be able to add many product attributes at once

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a non-translatable text product attribute "Author"
        And the store has a non-translatable text product attribute "ISBN"
        And I am logged in as an administrator

    @ui @javascript @api
    Scenario: Adding two text attributes to a product
        When I want to create a new configurable product
        And I specify its code as "HARRY_POTTER_1"
        And I name it "Harry Potter and the Sorcerer's Stone" in "English (United States)"
        And I set its non-translatable "Author" attribute to "J.K. Rowling"
        And I set its non-translatable "ISBN" attribute to "978-1338878929"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Harry Potter and the Sorcerer's Stone" should appear in the store
        And non-translatable attribute "Author" of product "Harry Potter and the Sorcerer's Stone" should be "J.K. Rowling"
        And non-translatable attribute "ISBN" of product "Harry Potter and the Sorcerer's Stone" should be "978-1338878929"
