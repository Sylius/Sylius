@viewing_products
Feature: Viewing product's associations
    In order to quickly navigate to other products associated with the one I'm currently viewing
    As a Visitor
    I want to see related products when viewing product details

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "LG G3"
        And the store has "LG headphones" and "LG earphones" products
        And the store has also "LG G4" and "LG G5" products
        And the store has a product association type "Accessories"
        And the store has also a product association type "Alternatives"
        And the product "LG G3" has an association "Accessories" with products "LG headphones" and "LG earphones"
        And the product "LG G3" has also an association "Alternatives" with products "LG G4" and "LG G5"

    @ui
    Scenario: Viewing a detailed page with product's associations
        When I view product "LG G3"
        Then I should see the product association "Accessories" with products "LG headphones" and "LG earphones"
        And I should also see the product association "Alternatives" with products "LG G4" and "LG G5"
