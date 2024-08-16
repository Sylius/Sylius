@managing_promotions
Feature: Adding promotion in different languages
    In order to see the label of promotion in a specific language
    As an Administrator
    I want to be able to add a promotion and specify labels in different languages

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a promotion with a label in a different language
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I specify its label as "W pełni metalowa promocja" in "Polish (Poland)" locale
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should appear in the registry
        And the "Full metal promotion" promotion should have a label "W pełni metalowa promocja" in "Polish (Poland)" locale
