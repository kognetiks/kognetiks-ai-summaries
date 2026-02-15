# Past Updates

## What's new in Version 1.0.5 - Released TBD

* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.4.

## What's new in Version 1.0.4 - Released 2026-02-15

### New Features
* **Post Excerpt Replacement**: Added support for replacing the `post_excerpt` field in the posts table with AI-generated summaries, so WordPress uses your AI summary wherever the theme displays the built-in excerpt.
* **Editable LLM Instructions**: Exposed configurable instruction prompts for summary generation, category generation, and tag generation in settings (Summaries tab), so you can tune how the AI produces excerpts and metadata.

### Improvements
* **Summaries tab**: New settings tab for summary/category/tag instructions and options.
* **Refresh All Summaries**: When you run "Refresh all summaries" from Tools, the plugin now also updates the `post_excerpt` field for affected posts so excerpts stay in sync.
* **Category, tag, and excerpt generation**: Refined behavior when a post is first published or touched so category, tag, and excerpt generation run as intended without unnecessary repeated refresh.

### Bug Fixes
* Resolved repeated excerpt refresh so summaries are not regenerated unnecessarily.
* Resolved recurring post excerpt regeneration to prevent duplicate or conflicting updates.
* Maintenance and stability fixes identified after release of 1.0.3.

## What's new in Version 1.0.3 - Released 2025-12-07

* **Google Gemini API Integration**: Added support for Google Gemini's API to provide advanced conversational capabilities for summaries.
* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.2.

## What's new in Version 1.0.2 - Released 2025-04-24

* **Mistral API Integration**: Added support for Mistral's API to provide advanced conversational capabilities for summaries.
* **Local AI Server**: Added support for a local AI server to generate summaries using JAN.AI and Hugging Face GGUF models.
* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.1.

## What's new in Version 1.0.1

* **DeepSeek API Integration**: Added support for DeepSeek's API to provide advanced conversational capabilities for summaries.
* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.0.

## What's new in Version 1.0.0

* **Initial Release**



