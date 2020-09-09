@managing_product_associations
Feature: Product association type unique type and owner
    In order to uniquely identify product association
    As an Administrator
    I want to be prevented from adding two product association with the same owner and the same type

    Background:
        Given the store has a product association type "Blue"
        And the store has a product "Blue jean"
        And the store has a product "Blue tee-shirt"
        And the product "Blue jean" has an association "Blue" with product "Blue tee-shirt"
        And I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a new product association with already taken information
        When I want to associate products
        And With the product association type "blue"
        And I want the product "blue_jean" to be the source
        And I want to associate the product "blue_tee_shirt"
        And I add it
        Then I should be notified that product association with those informations already exist
