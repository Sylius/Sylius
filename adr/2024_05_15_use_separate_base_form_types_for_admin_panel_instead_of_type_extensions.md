# Use Separate Base Form Types Instead of Type Extensions

* Status: Accepted
* Date: 2024-05-15

## Context and Problem Statement

In our current system, every base form type is placed at the lowest possible level, either within a specific bundle or the core. 
Form extensions serve as the primary means of extending form types since all form types are final. 
However, this approach leads to a significant issue: form extensions work globally, which prevents us from reusing any form type easily in different contexts. 
For example, if a field is added through an extension in the AdminBundle (admin context), the same field will also appear in the shop's context.
This problem is compounded by the introduction of Symfony UX, which necessitates overriding some fields to enable its functionality. 
As a result, distinguishing which context added a specific change and when each change should be applied becomes exceedingly difficult.

## Decision Drivers

* Need for context-specific form type customization.
* Avoiding global side effects caused by form extensions.
* Clear separation between admin and shop form types.
* Simplification of managing and overriding form fields with Symfony UX.

## Considered Options

### Use Separate Form Types

* **Good, because:**
    * Provides clear separation between admin and shop form types.
    * Allows context-specific customization without affecting other contexts.
    * Simplifies debugging and maintenance.
    * Facilitates the integration and management of Symfony UX-specific overrides.

* **Bad, because:**
    * Vastly increases the number of form types, leading to potential redundancy.
    * Requires additional effort to create and maintain separate form types.

### Continue Using Type Extensions

* **Good, because:**
    * Fewer form types to manage.
    * Leverages existing infrastructure without requiring significant changes.
    * Easier customization, you do not need to know where a form is used.

* **Bad, because:**
    * Form extensions affect all contexts globally, leading to unintended side effects.
    * Difficult to distinguish and manage context-specific changes.
    * Adding a specific functionality only in one context is extremely tedious.
    * Complicates integration with Symfony UX in AdminBundle due to overlapping overrides.

## Decision Outcome

Chosen option: "Use Separate Form Types", because it provides a clear and manageable way to customize form types for different contexts.
This approach resolves the issues of global side effects and the difficulty in distinguishing context-specific changes, making it easier to integrate with Symfony UX and maintain the system.

## References

* [Use Separate Form Types Example](https://github.com/Sylius/Sylius/pull/16257)
* [Continue Using Type Extensions Example](https://github.com/Sylius/Sylius/pull/15896)
