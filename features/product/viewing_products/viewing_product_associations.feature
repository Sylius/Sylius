@viewing_products
Feature: Viewing product's associations
    In order to quickly navigate to other products associated with the one I'm currently viewing
    As a Visitor
    I want to see related products when viewing product details

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a product "LG G3"
        And the store has "LG headphones", "LG earphones", "LG G4" and "LG G5" products
        And the store has a product association type named "Accessories" in "English (United States)" locale and "Akcesoria" in "Polish (Poland)" locale
        And the store has also a product association type named "Alternatives" in "English (United States)" locale and "Alternatywy" in "Polish (Poland)" locale
        And the product "LG G3" has an association "Accessories" with products "LG headphones" and "LG earphones"
        And the product "LG G3" has also an association "Alternatives" with products "LG G4" and "LG G5"

    @ui
    Scenario: Viewing a detailed page with product's associations in default locale
        When I view product "LG G3"
        Then I should see the product association "Accessories" with products "LG headphones" and "LG earphones"
        And I should also see the product association "Alternatives" with products "LG G4" and "LG G5"

    @ui
    Scenario: Viewing a detailed page with product's associations after locale change
        When I view product "LG G3" in the "Polish (Poland)" locale
        Then I should see the product association "Akcesoria" with products "LG headphones" and "LG earphones"
        And I should also see the product association "Alternatywy" with products "LG G4" and "LG G5"
