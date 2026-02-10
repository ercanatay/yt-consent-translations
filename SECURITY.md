# Security Policy

## Supported Versions
Current supported version: **1.3.1**

## Reporting a Vulnerability
Please report any security vulnerabilities to the plugin author via the [GitHub Issues page](https://github.com/ercanatay/yt-consent-translations/issues).

## Security Audit Log
This log records automated security and quality audits performed on the codebase.

### 2026-02-07
**Status: PASSED**
*   **Auditor:** Automated Agent
*   **Scope:**
    *   **Security:** Verified nonces, sanitization, permissions, SQLi/XSS prevention.
    *   **Quality:** Checked PHP syntax (`php -l`), coding standards, and unused code.
    *   **Data Integrity:** Validated 36 JSON translation files.
    *   **Consistency:** Verified version numbers.
*   **Findings:** 0 issues found.

### 2026-02-10
**Status: PASSED**
*   **Auditor:** Sentinel Agent
*   **Scope:**
    *   **Security Fix:** Implemented `wp_kses` sanitization in `YTCT_Options::sanitize_options` to prevent Stored XSS.
    *   **Verification:** Verified with `tests/test_options_security.php` and existing test suite.
    *   **Automated Audit:** Ran `scripts/scan_code.py` and `scripts/validate_json.py`.
*   **Findings:**
    *   Fixed potential Stored XSS vulnerability in options storage.
    *   All automated tests passed.
