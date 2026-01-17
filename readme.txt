=== SudoWP PostGallery (Security Fork) ===
Contributors: SudoWP, WP Republic
Original Authors: RTO GmbH
Tags: gallery, post gallery, security-fork, upload-fix, patched
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 1.12.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A security-hardened fork of the abandoned "PostGallery" plugin. Fixes critical Arbitrary File Upload vulnerabilities.

== Description ==

This is SudoWP PostGallery, a community-maintained and security-hardened fork of the "PostGallery" plugin, which was closed/abandoned on WordPress.org due to security issues.

**Why this fork?**
The original plugin contained a critical Arbitrary File Upload vulnerability (CVE-2025-13543) and allowed unauthenticated users (guests) to attempt file uploads. This fork patches these issues to make the plugin safe for production use again.

**Original Plugin:** [PostGallery on WordPress.org](https://wordpress.org/plugins/postgallery/) (Closed)

**Security Patches in SudoWP Edition:**
* **Critical Fix (CVE-2025-13543):** Implemented strict file validation. Only specific image types (JPG, PNG, GIF, WEBP) are allowed. PHP, EXE, and other executable files are strictly blocked.
* **Guest Access Removed:** Removed nopriv AJAX hooks. Unauthenticated users can no longer trigger the upload mechanism.
* **Capability Checks:** Added current_user_can('upload_files') checks to all administrative AJAX endpoints (upload, delete, rename, rotate).
* **MIME Type Verification:** Added finfo_file checks to verify the actual file content type, preventing renamed malicious files from bypassing filters.
* **Sanitization:** Enforced sanitize_file_name() on all uploaded files.

**Key Features Preserved:**
* Attach a gallery to any post.
* Customizable templates.
* Drag & Drop upload interface.
* Elementor Widget support.

== Installation ==

1. Upload the `sudowp-postgallery` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. The gallery metabox will appear on your Posts/Pages.

== Frequently Asked Questions ==

= Is this compatible with the original PostGallery? =
Yes, it is a direct drop-in replacement. However, you should delete the original abandoned plugin to ensure the vulnerability is removed from your server.

= Why was the original closed? =
The original plugin was closed by the WordPress Security Team on December 2, 2025, due to the unpatched security issues we have fixed in this fork.

== Changelog ==

= 1.12.6 (SudoWP Edition) =
* Security Fix: Patched Critical Arbitrary File Upload (CVE-2025-13543).
* Security Fix: Removed guest/public access to upload functions.
* Security Fix: Implemented strict Allowlist for file extensions and MIME types.
* Maintenance: Rebranded as SudoWP PostGallery.