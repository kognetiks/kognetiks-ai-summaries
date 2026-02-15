# Kognetiks AI Summaries — Functional Specification

## Overview

**Kognetiks AI Summaries** is a WordPress plugin that generates concise, AI-powered excerpts (summaries) for posts, pages, and other content. It integrates with multiple AI platforms so you can replace or supplement WordPress’s default excerpts with LLM-generated summaries. Summaries are stored locally and only regenerated when content changes, keeping API usage and cost low.

**Version:** 1.0.4  
**License:** GPLv3 or later  
**WordPress Compatibility:** Tested up to WordPress 6.9

---

## Core Functionality

### Excerpt Integration

WordPress traditionally provides excerpts in two ways: automatic (first 55 words, stripped of HTML) or manual (user-entered in the Excerpt field). Kognetiks AI Summaries adds a third:

- **AI-generated excerpts**: When the theme or code calls `get_the_excerpt`, the plugin can return a stored AI summary instead of the default or manual excerpt.
- **Display locations**: AI summaries appear wherever WordPress would normally show an excerpt (e.g. archive pages, search results, homepage loops, category/tag archives).
- **Storage**: Summaries are stored in a custom database table (`{prefix}kognetiks_ai_summaries`). Manual excerpts in the post editor are not overwritten unless you enable **Post Excerpt Replacement** (see below).
- **Staleness**: A summary is regenerated only when the post’s modification date has changed since the last generation, so each piece of content is summarized once per edit.

### Post Excerpt Replacement (Optional)

You can optionally write the AI summary into the `post_excerpt` column in the `wp_posts` table:

- **Do Not Replace** (default): Only the plugin’s custom table is used; `post_excerpt` is left unchanged.
- **Replace**: When adding or updating an AI summary, the plugin overwrites `post_excerpt` with the summary (trimmed to the configured word count).
- **Replace if Blank**: The plugin updates `post_excerpt` only when it is empty; existing manual excerpts are kept.

This ensures themes and plugins that read `post_excerpt` directly (instead of the `get_the_excerpt` filter) also see the AI summary when desired.

### AI Platform Integration

The plugin supports one active AI platform at a time, selected in **General** settings. Supported platforms:

| Platform   | Role |
|-----------|------|
| **OpenAI** | Chat Completions API; models such as GPT-3.5-turbo, GPT-4, GPT-4o, etc. |
| **NVIDIA NIM** | NVIDIA Inference Microservices; various NVIDIA-optimized models. |
| **Anthropic** | Claude models (e.g. Claude 3.5 Sonnet, Haiku, Opus). |
| **DeepSeek** | DeepSeek chat and reasoner models. |
| **Mistral** | Mistral chat models. |
| **Google** | Google Gemini API (e.g. gemini-2.0-flash). |
| **Local (JAN.AI)** | Local server (e.g. JAN.AI) with Hugging Face GGUF–style models; no external API. |

For each cloud provider you configure:

- **API key** (required for that platform).
- **Model choice** (dropdown populated from the provider’s API where supported).
- **Advanced settings** (e.g. max tokens, temperature, timeout) where exposed in the UI.

Summaries, categories, and tags are all generated via the same platform’s chat/completion endpoint using configurable instruction prompts.

### Summary, Category, and Tag Generation

- **Summaries**: The plugin sends post content to the selected AI with a configurable **Summary Instructions** prompt; the word count from **General** (e.g. 55 words) is appended. The model returns summary text only; the plugin strips common prefixes and stores the result.
- **Categories**: If **Generate Categories** is enabled (Summaries tab), the plugin can request a comma-separated list of one-word categories. **Categories Instructions** and the category count from settings are used. New categories are created if they do not exist; existing categories are assigned.
- **Tags**: If **Generate Tags** is enabled, the plugin requests comma-separated tags in the same way using **Tags Instructions** and the tag count. Tags are created or assigned as needed.
- **Post types**: **Enabled Post Types** (Summaries tab) controls which post types (e.g. Post, Page, custom types) receive AI summaries and metadata. Only enabled types trigger generation and display.

### When Generation Runs

- **On demand (front-end)**: When a visitor or crawler requests a page that calls `get_the_excerpt` for a post (e.g. single post view or archive), the plugin checks for an existing summary and whether it is stale. If missing or stale, it may generate (or return cached) subject to per-request limits to avoid timeouts.
- **On publish/update**: When a post is saved or published, the plugin can trigger generation so the summary is ready before the next front-end request.
- **Tools → Refresh All Summaries**: Bulk regeneration for all posts that already have summaries; also updates `post_excerpt` when **Post Excerpt Replacement** is enabled. Use with care (API cost and time).

---

## Admin Interface

### Settings Tabs

- **General**: AI platform choice, Turn AI Summaries On/Off, AI Summaries Length (words), Post Excerpt Replacement (Do Not Replace / Replace / Replace if Blank).
- **API / [Platform]**: One tab per provider (OpenAI, NVIDIA, Anthropic, DeepSeek, Mistral, Google, Local). API key, model choice, and any advanced options (timeout, max tokens, etc.).
- **Summaries**: LLM instruction prompts (Summary, Categories, Tags), Generate Categories / Generate Tags toggles, Enabled Post Types checkboxes.
- **Diagnostics**: System/plugin info, API status test, Plugin Diagnostics level (Off / Success / Notice / Failure / Warning / Error), Custom Error Message, Suppress Notices and Warnings, Delete Plugin Data on Uninstall.
- **Tools**: Options Exporter, Manage Error Logs, Clean-up Tools (see below).
- **Support**: Links and support information.

### Tools

- **Options Exporter**: Export plugin options to CSV or JSON (API keys excluded). Useful for backup or migration.
- **Manage Error Logs**: View, download, or delete plugin error log files stored under the uploads directory.
- **Clean-up Tools** (all require confirmation and appropriate capability):
  - **Delete All Summaries**: Removes all rows from the AI summaries table; does not change posts or `post_excerpt`.
  - **Refresh All Summaries**: Regenerates summaries for all posts that currently have one; respects Post Excerpt Replacement.
  - **Convert to Proper Case**: Updates existing category and tag names to proper case (e.g. “acoustics” → “Acoustics”).
  - **Delete Empty Categories**: Removes categories with zero assigned posts.
  - **Delete Empty Tags**: Removes tags with zero assigned posts.
  - **Delete Orphaned Summaries**: Removes stored summaries whose post no longer exists.

---

## Configuration Options

### General Settings

- **AI Platform Choice**: OpenAI, NVIDIA, Anthropic, DeepSeek, Mistral, Google, or Local.
- **Turn AI Summaries On/Off**: Master switch for replacing excerpts with AI summaries.
- **AI Summaries Length (Words)**: Target length (e.g. 55); used in the summary prompt and for trimming.
- **Post Excerpt Replacement**: Do Not Replace, Replace, or Replace if Blank.

### API Configuration (per platform)

- **API Key**: Stored in options; required for the selected platform.
- **Model Choice**: Dropdown of models (from provider API where supported).
- **Advanced Settings**: Timeout, max tokens, temperature, base URL (e.g. Local), etc., as exposed in the UI.

### Summaries (LLM Instructions and Taxonomy)

- **Summary Instructions**: Prompt prefix for summary generation; word count is appended automatically.
- **Categories Instructions**: Prompt prefix for category suggestions; category count appended.
- **Tags Instructions**: Prompt prefix for tag suggestions; tag count appended.
- **Generate Categories** / **Generate Tags**: Enable or disable automatic category/tag generation when generating metadata.
- **Enabled Post Types**: Checkboxes for each public post type; only checked types get summaries and metadata.

### Diagnostics

- **Plugin Diagnostics**: Logging level (Off through Error).
- **Custom Error Message**: Message shown to users when the plugin encounters an error (e.g. API failure).
- **Suppress Notices and Warnings**: Reduces admin notices.
- **Delete Plugin Data on Uninstall**: When set to “Yes”, uninstall removes options, transients, cron events, log/debug files, and the custom table (see Uninstall below).

---

## Technical Details

### Database

- **Table**: `{wpdb->prefix}kognetiks_ai_summaries`
  - `id` (mediumint, auto increment)
  - `post_id` (mediumint, unique)
  - `ai_summary` (text)
  - `post_modified` (datetime, used for staleness)
- Table is created or upgraded via `dbDelta` on activation or when missing (admin context only; not during front-end `get_the_excerpt`).

### WordPress Integration

- **Filter**: `get_the_excerpt` — Replaces or supplies the excerpt with the stored AI summary when summaries are enabled and a valid summary exists; falls back to `post_excerpt` or default behavior as appropriate.
- **Hooks**: Generation can be triggered on save/publish and from Tools; per-request generation is limited (e.g. via `kognetiks_ai_summaries_generations_per_request`) to avoid timeouts on archive/search pages.
- **Plugin action links**: Settings and Support links on the Plugins screen.
- **Uninstall**: If **Delete Plugin Data on Uninstall** is “Yes”, `uninstall.php` runs the option-driven cleanup (options, transients, cron, log/debug files, custom table). If “No”, data is left in place.

### Security and Privacy

- **Capability**: Admin actions (settings, tools, export, log management) require `manage_options`.
- **Nonces**: State-changing actions (save settings, cleanup tools, download/delete logs, options export) use nonces.
- **API keys**: Stored in WordPress options; excluded from options export.
- **File handling**: Log download/delete use `sanitize_file_name`, basename, and path containment (`realpath`) to prevent directory traversal.
- **SQL**: Dynamic queries use `$wpdb->prepare`.
- **External requests**: All API calls use the WordPress HTTP API to the configured provider; Local server URL is admin-configured.

---

## Installation and Setup

1. Install and activate the plugin.
2. In **General**, choose the AI platform and set **Turn AI Summaries** to **On** after configuration.
3. In the corresponding **API / [Platform]** tab, enter the API key and save; select the desired model.
4. In **Diagnostics**, run the API test to confirm connectivity.
5. Optionally adjust **Summaries** (instructions, category/tag generation, enabled post types) and **Post Excerpt Replacement** in General.
6. Publish or update a post, or use **Tools → Refresh All Summaries**, to generate summaries. They will also be created on demand when excerpts are requested on the front-end (subject to limits).

---

## Use Cases

- **Search and archives**: Show AI summaries in search results and on category/tag/date archive pages instead of the first 55 words.
- **Homepage and grids**: Use AI summaries in theme loops that display excerpts.
- **SEO and AEO**: Provide consistent, readable summaries for snippets and previews.
- **Consistency**: One place to control summary length and tone via instructions; optional sync to `post_excerpt` for themes that read it directly.
- **Multi-platform**: Switch providers (OpenAI, Google, Local, etc.) without changing content; only the generation backend changes.

---

## Support and Documentation

- **Documentation**: In-repo `documentation/` (overview, general, API settings, summaries, diagnostics, tools, support, updates).
- **Support**: https://kognetiks.com/plugin-support/
- **External services**: readme and in-plugin copy disclose use of third-party AI APIs and link to each provider’s terms and privacy policy.

---

## Compliance and Data

- **Data handling**: Summaries and options are stored in the WordPress database; “Delete Plugin Data on Uninstall” controls removal on uninstall.
- **External services**: Users must obtain API keys and agree to each provider’s terms and privacy policy (OpenAI, NVIDIA, Anthropic, DeepSeek, Mistral, Google, JAN.AI) as documented in the plugin and readme.

---

## Summary

Kognetiks AI Summaries adds AI-powered excerpts to WordPress by plugging into `get_the_excerpt` and optionally into `post_excerpt`. One of seven AI platforms is used to generate summaries (and optionally categories and tags) with configurable instructions and word counts. Storage is local and regeneration is driven by content changes and optional bulk tools, keeping API usage predictable. The plugin is suitable for sites that want consistent, editable excerpt copy for search, archives, and theme loops without manual excerpt entry.

---

**[Back to the Overview](/overview.md)**
