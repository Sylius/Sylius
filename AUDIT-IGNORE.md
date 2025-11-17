# AUDIT-IGNORE

This document explains why specific advisories are added to `composer.json` → `config.audit.ignore`.

**PKSA-gs8r-6kz6-pp56** — `api-platform/core` CVE-2025-31485; affected versions < 3.4.17, 4.0.0–4.0.21, 4.1.0–4.1.4 are pulled by Sylius dependency constraints. GraphQL property security grant caching issue allows unauthorized access.
https://www.cve.org/CVERecord?id=CVE-2025-31485

**PKSA-gnn4-pxdg-q76m** — `api-platform/core` CVE-2025-31481; same affected versions as above. GraphQL security bypass via Relay `node` type allows unauthorized entity access.
https://www.cve.org/CVERecord?id=CVE-2025-31481

**PKSA-yhcn-xrg3-68b1** — `twig/twig` CVE-2024-45411; affected versions < 1.44.8, < 2.16.1, < 3.14.0 are pulled by Sylius dependency constraints. Sandbox security checks can be bypassed when templates are loaded in non-sandbox context before include().
https://www.cve.org/CVERecord?id=CVE-2024-45411

**PKSA-2wrf-1xmk-1pky** — `twig/twig` CVE-2024-51755; affected versions < 3.11.2 or 3.12.0–3.14.0 are pulled by Sylius dependency constraints. Unguarded `__isset()` and array-access in sandbox allows attribute access on Array-like objects.
https://www.cve.org/CVERecord?id=CVE-2024-51755
