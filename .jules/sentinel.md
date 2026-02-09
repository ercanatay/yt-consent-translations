## 2024-05-23 - Reverse Tabnabbing Prevention with Core Functions
**Vulnerability:** Sanitization of HTML anchor tags allowed `target="_blank"` without enforcing `rel="noopener noreferrer"`, exposing users to Reverse Tabnabbing attacks.
**Learning:** WordPress 5.1+ includes `wp_targeted_link_rel()` which automatically adds `rel="noopener"` to links with `target`. This is safer and more robust than manual regex replacement or DOM manipulation.
**Prevention:** When allowing `target` attribute in `wp_kses`, always post-process the string with `wp_targeted_link_rel()` (wrapped in `function_exists` for backward compatibility) to ensure security best practices are enforced automatically.
