# SudoWP PostGallery (Security Fork)

**Contributors:** SudoWP, WP Republic  
**Original Authors:** RTO GmbH  
**Tags:** gallery, security, patched, cve-2025-13543  
**Requires at least:** 5.8  
**Tested up to:** 6.7  
**Stable tag:** 1.12.6  
**License:** GPLv2 or later  

## Security Notice
This is a **security-hardened fork** of the abandoned "PostGallery" plugin. The original plugin was closed on WordPress.org on Dec 2, 2025, due to severe security vulnerabilities.

**Original Plugin Link:** [https://wordpress.org/plugins/postgallery/](https://wordpress.org/plugins/postgallery/)

---

## Description

**SudoWP PostGallery** restores the functionality of the popular PostGallery plugin while fixing critical security flaws that left sites vulnerable to hacking.

### Security Patches Implemented
We have conducted a full code audit and applied the following fixes:

1.  **Arbitrary File Upload Fix (CVE-2025-13543):**
    * The original uploader accepted almost any file type. We implemented a strict **Allowlist** that permits *only* `jpg`, `jpeg`, `png`, `gif`, and `webp`.
    * Added **MIME Type Validation** using `finfo_file` to prevent extension spoofing (e.g., uploading a `.php` file renamed to `.jpg`).

2.  **Access Control Hardening:**
    * **Removed Guest Access:** The original code allowed unauthenticated users (`nopriv` hooks) to access the upload handler. These hooks have been removed.
    * **Capability Checks:** Added `current_user_can('upload_files')` to all sensitive AJAX actions (`delete`, `rename`, `rotate`, `save_meta`).

3.  **Input Sanitization:**
    * Enforced WordPress native `sanitize_file_name()` on all file inputs.

## Installation

1.  Download the repository.
2.  **Important:** Deactivate and delete the original "PostGallery" plugin if installed.
3.  Upload the `sudowp-postgallery` folder to your `/wp-content/plugins/` directory.
4.  Activate the plugin.

## Changelog

### Version 1.12.6 (SudoWP Edition)
* **Security Fix:** Patched Critical Arbitrary File Upload vulnerability.
* **Security Fix:** Restricted upload functionality to authenticated administrators only.
* **Security Fix:** Implemented strict file type validation engine.
* **Rebrand:** Forked as SudoWP PostGallery.

---
*Maintained by the SudoWP Security Project.*