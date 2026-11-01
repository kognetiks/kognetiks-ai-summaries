# Past Updates

## What's new in Version 1.0.6 - Release TBD

* **Summaries Cutoff**: Anthropic models that think before they answer, including Claude Haiku 5.5, no longer fail or stop mid-sentence. The plugin reads the summary from the text block, turns off up-front thinking where the model allows it, and discards a reply that runs out of tokens before the sentence is finished.
* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.5.

## What's new in Version 1.0.5 - Released 2026-10-31

* **Refresh All Summaries**: Refresh All now runs in the background, one post at a time. You can leave the Tools page. The same queue continues on a later visit. Progress shows the total, successful, skipped, failed, and remaining counts.
* **Pauses and retries**: Invalid API credentials or an invalid model pause the run until you correct the settings and resume. Temporary provider errors are retried. A failed regeneration keeps the existing summary and excerpt.
* **Refresh scope**: The queue includes published posts of enabled types that already have a summary. Refresh All does not generate or change categories or tags. Excerpt updates still follow the Do Not Replace, Replace if Blank, or Replace setting.
* **Skipped posts**: A queued post is counted as skipped, not failed, when it is no longer published, its post type is no longer enabled, the post has been deleted, or its summary record has been removed. A temporary database read failure leaves that post in the queue.
* **New and updated posts**: A newly published post is summarized once. Editing the content of a published post regenerates that summary. Changing only the title does not. Category and tag suggestions are requested only when the post does not already have them.
* **Excerpt display**: Building an empty excerpt no longer runs the post content, shortcodes, or prompt lookups. Manual excerpts and stored summaries are left unchanged.
* **Local AI server**: The model list is loaded from the configured JAN.AI server. Diagnostics reports whether that connection succeeded.
* **Anthropic models**: The model list uses the current Anthropic models. A saved model that is no longer offered is replaced before the next request.
* **Translations**: Added a translation template. The text domain now matches the plugin slug, `kognetiks-ai-summaries`.

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



