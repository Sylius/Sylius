@viewing_products
Feature: Viewing product's associations
    In order to quickly navigate to other products associated with the one I'm currently viewing
    As a Visitor
    I want to see related products when viewing product details

    Background:
        Given the store operates on a channel named "Smartphone Store" with hostname "smartphone.shop"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store also operates on another channel named "Notebook Store" with hostname "notebook.shop"
        And the "Smartphone Store" channel has a product "LG G3"
        And the "Smartphone Store" channel has "LG headphones", "LG earphones", "LG G4" and "LG G5" products
        And the "Notebook Store" channel has "LG Gram" and "LG AC Adapter" products
        And the store has a product association type named "Accessories" in "English (United States)" locale and "Akcesoria" in "Polish (Poland)" locale
        And the store has also a product association type named "Alternatives" in "English (United States)" locale and "Alternatywy" in "Polish (Poland)" locale
        And the product "LG G3" has an association "Accessories" with products "LG headphones" and "LG earphones"
        And the product "LG G3" has also an association "Alternatives" with products "LG G4" and "LG G5"
        And the product "LG Gram" has an association "Alternatives" with products "LG AC Adapter" and "LG headphones"

    @ui @api
    Scenario: Viewing a detailed page with product's associations in default locale
        Given I am browsing channel "Smartphone Store"
        When I view product "LG G3"
        Then I should see the product association "Accessories" with products "LG headphones" and "LG earphones"
        And I should also see the product association "Alternatives" with products "LG G4" and "LG G5"

    @ui @api
    Scenario: Viewing a detailed page with product's associations after locale change
        Given I am browsing channel "Smartphone Store"
        When I view product "LG G3" in the "Polish (Poland)" locale
        Then I should see the product association "Akcesoria" with products "LG headphones" and "LG earphones"
        And I should also see the product association "Alternatywy" with products "LG G4" and "LG G5"

    @ui @api
    Scenario: Viewing a detailed page with product's associations within current channel
        Given I am browsing channel "Notebook Store"
        When I view product "LG Gram"
        Then I should see the product association "Alternatives" with product "LG AC Adapter"
        And I should not see the product association "Alternatives" with product "LG headphones"

    @ui @api
    Scenario: Viewing a detailed page with enabled associated products only
        Given the "LG G4" product is disabled
        And I am browsing channel "Smartphone Store"
        When I view product "LG G3"
        Then I should see the product association "Accessories" with products "LG headphones" and "LG earphones"
        And I should also see the product association "Alternatives" with product "LG G5"
        And I should not see the product association "Alternatives" with product "LG G4"

    @ui @api
    Scenario: Viewing a detailed page while an empty association exists
        Given products "LG G4" and "LG G5" are disabled
        And I am browsing channel "Smartphone Store"
        When I view product "LG G3"
        Then I should see the product association "Accessories" with products "LG headphones" and "LG earphones"
        And I should not see the product association "Alternatives"
