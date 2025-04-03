=== Kognetiks AI Summaries ===
Contributors: Kognetiks
Tags: AI, Excerpts, Search, ChatGPT, Claude
Donate link: https://kognetiks.com/wordpress-plugins/donate/
Tested up to: 6.7.1
Stable tag: 1.0.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Kogentiks AI Summaries - Effortless Excerpts, Power by Intelligence.

== Description ==

**Kognetiks AI Summaries**: Transforming Content with AI-Powered Precision

Elevate your website with **Kognetiks AI Summaries**, the ultimate plugin for crafting compelling, AI-generated excerpts that engage and inform. Seamlessly integrate cutting-edge artificial intelligence into your WordPress site to produce concise, insightful summaries for pages, posts, and other content — effortlessly enhancing user experience and site navigation.

Powered by industry-leading AI platforms like OpenAI, NVIDIA, Anthropic and DeepSeek, Kognetiks AI Summaries leverages advanced algorithms through robust APIs to deliver polished, impactful excerpts. Whether for your homepage, archive pages, or search results, these AI-crafted summaries ensure your visitors encounter clear, captivating content at every turn. Stay ahead with innovation that makes your content shine.

**Important Note:** This plugin requires an API key from OpenAI, NVIDIA or other AI platform vendors, to function correctly.

== External Services ==

This plugin relies on external AI services for generating summaries of your pages and posts. It sends your content to a third-party AI provider for summarization. Once summarized, the summary excerpt is stored locally in your database and is not regenerated again unless there has been a change indicated by the modification date of your content. By using this plugin, you agree to abide by each service’s terms of service and privacy policy:

- **OpenAI**: [Terms of Use](https://platform.openai.com/terms) | [Privacy Policy](https://openai.com/policies/privacy-policy/)
- **NVIDIA**: [Terms of Use](https://www.nvidia.com/en-us/about-nvidia/nv-accounts/) | [Privacy Policy](https://www.nvidia.com/en-us/about-nvidia/privacy-policy/)
- **Anthropic**: [Terms of Service](https://www.anthropic.com/legal/consumer-terms) | [Privacy Policy](https://docs.anthropic.com/en/docs/legal-center/privacy)
- **DeepSeek**: [Terms of Use](https://chat.deepseek.com/downloads/DeepSeek%20User%20Agreement.html) | [Privacy Policy](https://chat.deepseek.com/downloads/DeepSeek%20Privacy%20Policy.html)

**IMPORTANT**:

- This plugin requires an API key from OpenAI, NVIDIA, Anthropic or DeepSeek to function. Without an API key, the plugin cannot process summaries.

- Obtain API keys here:

    - [OpenAI API Keys](https://platform.openai.com/account/api-keys)
    - [NVIDIA API Keys](https://developer.nvidia.com/nim)
    - [Anthropic API Keys](https://www.anthropic.com/)
    - [DeepSeek API Keys](https://platform.deepseek.com/sign_in)

- By entering an API key from the AI provider of your choice and activating the plugin, you:

    - Consent to sending your content to the selected AI provider for processing and summarization.
    - Agree to abide by their terms of service, pricing, and privacy policy.
    - Acknowledge that your data, including text submitted, may be transferred to and processed by the AI platform.

**NOTE**: If no API key is provided, or if communication with the selected service fails, the plugin will not generate summaries.

== Installation ==

**Installing the Kognetiks AI Summaries Plugin on Your Site**

1. From the `General` tab on the **Kognetiks AI Summaries** settings page, select from one of three AI Platform Choices: `OpenAI` (the default), `NVIDIA`, or `Anthropic`.

2. Before you can `Turn AI Summaries` to `On`, you'll need to set up an account with the AI Platform vendor of your choice.  The plugin is very efficient when it comes to generating AI excerpts of your content.

    - The plugin will only generate a summary of a page, port or other published content once.  But, it will check to see if the content has been updated or modified.  If the page or post has changed since the last time the summary was generated, a new summary will be created.  This makes incorporating AI generated excerpts very cost affordable.
    - AI generated excerpts appear where ever WordPress would present an excerpt on your site.  It **does not** replace any hand-crafted excepts you might have added to posts, pages or other content.  AI summaries are in addition to these excerpts and are stored in a separate table on your site.  Once generated, they won't be regenerated unless the content has been updated.

3. From the `API/OpenAI`, `API/NVIDIA`, or `API/Anthropic` tab (depending on the `AI Platform Choice` you made on the `General` tab), enter your `API Key`, then click the `Save Settings` button at the bottom of the page.

4. Once you have saved your API key, the list of currently available models from the AI Platform vendor of your choice will be available in the `Model Choice` pull down menu.  In most cases, the `Model Choice` will default to the latest model.  If you changes models, remember to click the `Save Settings` button at the bottom of the page.

5. In most cases you should not need to change the `Advanced Settings` defaults.

6. You can check that everything is working as expected by clicking on the `Diagnostics` tabs.  Once the page has loaded, the `API STATUS` will reflect `Success: Connection to the OpenAI API was successful!`.  If there is a problem with the API service or your account, a more detail error message will be presented.

7. Now that you have configured the plugin, return to the `General` tab, and `Turn AI Summaries On/Off` by selecting `On`.  Then choose the `AI Summaries Length (Words)` from the pulldown (the default is a brief summary of only 55 words).  Remember to click the `Save Settings` button at the bottom of the page.

8. Now you're ready to generate summaries.  Use any page on your site where a summary might appear, such as `Search`, to see an automatically crafted, concise, and insightful summarization of your content. These polished excerpts seamlessly enhance your site, offering visitors clear and impactful content wherever excerpts are displayed.

== Frequently Asked Questions ==

**Plugin Support**

Please visit [https://kognetiks.com/plugin-support/](https://kognetiks.com/plugin-support/).

For **frequently asked questions**, please visit [https://kognetiks.com/wordpress-plugins/frequently-asked-questions/](https://kognetiks.com/wordpress-plugins/frequently-asked-questions/).

**How do I get an API key?**

Sign up with one of the following AI platforms to obtain your API key:

- **OpenAI**

    [See API/OpenAI Settings](api-settings/api-openai-settings.md)

- **NVIDIA**

    [See API/NVIDIA Settings](api-settings/api-nvidia-settings.md)

- **Anthropic**

    [See API/Anthropic Settings](api-settings/api-anthropic-settings.md)

**Does the plugin support multiple languages?**

Yes, the **Kognetiks AI Summaries** plugin supports multiple languages, allowing you to cater to a diverse audience.

== Your Journey Towards an AI Enhanced Website Begins! ==

With the **Kognetiks AI Summaries** installed, you're now equipped to offer a more dynamic, engaging, and responsive experience to your website visitors.

== Disclaimer ==

WordPress, OpenAI, ChatGPT, NVIDIA, NIM, Anthropic, Claude, DeepSeek, and related trademarks are the property of their respective owners. Kognetiks is an independent entity and is not affiliated with, endorsed by, or sponsored by WordPress Foundation, OpenAI, NVIDIA, Anthropic or DeepSeek.

== Screenshots ==

1. General Settings
2. API Settings
3. Diagnostics
4. Tools
5. Support

== Changelog ==

= 1.0.2 =

* **Local AI Server**: Added support for a local AI server to generate summaries using JAN.AI and Hugging Face GGUF models.
* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.1.

= 1.0.1 =

* **DeepSeek API Integration**: Added support for DeepSeek's API to provide advanced conversational capabilities for the chatbot.
* **Bug Fixes**: Resolved minor issues and bugs identified after release of version 1.0.0.

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.0.0 =

* Initial release.
