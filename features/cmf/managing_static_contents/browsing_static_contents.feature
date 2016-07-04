@managing_static_contents
Feature: Browsing static contents
    In order to see all static contents in the store
    As an Administrator
    I want to browse static contents

    Background:
        Given the store has static contents "Krzysztof Krawczyk" and "Ryszard Rynkowski"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing static contents in store
        When I want to browse static contents of the store
        Then I should see 2 static contents in the list
        And I should see the static content "Krzysztof Krawczyk" in the list
