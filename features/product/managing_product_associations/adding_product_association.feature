@managing_product_associations
Feature: Adding a new product association
    In order to connect products together in many contexts
    As an Administrator
    I want to add a new product association to the store

    Background:
        Given the store has a product association type "Blue"
        And the store has a product "Blue jean"
        And the store has a product "Blue tee-shirt"
        And I am logged in as an administrator

    @ui @api
    Scenario: Associate products
        When I want to associate products
        And With the product association type "blue"
        And I want the product "blue_jean" to be the source
        And I want to associate the product "blue_tee_shirt"
        And I add it
        Then I should be notified that it has been successfully created
