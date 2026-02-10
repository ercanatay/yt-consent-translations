## 2024-05-22 - Stored XSS in Option Storage
**Vulnerability:** `YTCT_Options::sanitize_options` casted input to string but did not sanitize HTML, allowing Stored XSS if called with unsanitized data.
**Learning:** Helper classes named "sanitize" might be misleading if they don't actually sanitize for XSS. Always verify sanitization logic.
**Prevention:** Enforce `wp_kses` at the storage layer (in `update_options` or similar) to ensure data is safe regardless of where it comes from.
