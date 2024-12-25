# Getting Started

**Kognetiks AI Summaries** is a plugin that allows you to effortlessly integrate AI generated summaries of your pages and posts into your website, providing  powerful, AI-driven content for enhanced user experience and personalized support.

AI platforms - like those from OpenAI, NVIDIA, Anthropic, and others - use natural language processing and machine learning algorithms to interact with users in a human-like manner. They are designed to answer questions, provide suggestions, and engage in conversations with users. One of their outstanding capabilities is to ingest information and generate consise summaries.

The **Kognetiks AI Summaries** plugin is powered by OpenAI, NVIDIA, and Anthropic via their APIs and Large Langugage Models (LLMs) to bring artificial intelligence to life within your WordPress website.

**Important Note:** This plugin requires an API key from OpenAI, NVIDIA, or Anthropic to function correctly. You can obtain an API key by signing up with one of the vendors below:

- [OpenAI](https://platform.openai.com/account/api-keys)

- [NVIDIA](https://build.nvidia.com/nim)

- [Anthropic](https://console.anthropic.com)

## Steps

1. From the `General` tab on the **Kognetiks AI Summaries** settings page, select from one of three AI Platform Choices: `OpenAI` (the default), `NVIDIA`, or `Anthropic`.

2. Before you can `Turn AI Summaries` to `On`, you'll need to setup an account with the AI Platform vendor of your choice.  The plugin is very efficient when it comes to generating AI excerpts of your content.

    - The plugin will only generate a summary of a page, port or other published content once.  But, it will check to see if the content has been updated or modified.  If the page or post has changed since the last time the summary was generated, a new summary will be created.  This makes incorporating AI generated excerpts very cost affordable.
    - AI generated excerpts appear whereever WordPress would present a excerpt on your site.  It **does not** replace any hand-crafted excepts you might have added to posts, pages or other content.  AI summaries are in addition to these excerpts and are stored in a seperate table on your site.  Once generated, they won't be regenerated unless the content has been updated.

3. From the `API/OpenAI`, `API/NVIDIA`, or `API/Anthropic` tab (depending on the `AI Platform Choice` you made on teh `General` tab), enter your `API Key`, then click the `Save Settings` button at the bottom of the page.

4. Once you have saved your API key, the list of currently available models from the AI Platform vendor of your choice will be available in the `Model Choice` pulldown menu.  In most cases, the `Model Choice` will default to the latest model.  If you changes models, remember to click the `Save Settings` button at the bottom of the page.

5. In most cases you should not need to change the `Advanced Settings` defaults.

6. You can check that everything is working as expected by clicking on the `Diagnostics` tabs.  Once the page has loaded, the `API STATUS` will reflect `Success: Connection to the OpenAI API was successful!`.  If there is a problem with the API service or your account, a more detail error message will be presented.

7. Now that you have configured the plugin, return to the `General` tab, and `Turn AI Summaries On/Off` by selecting `On`.  Then choose the `AI Summaries Length (Words)` from the pulldown (the default is a brief summary of only 55 words).  Remember to click the `Save Settings` button at the bottom of the page.

8. Now you're ready to generate summaries.  Use any page on your site where a summary might appear, such as `Search`, to see an automatically crafted, concise, and insightful summarization of your content. These polished excerpts seamlessly enhance your site, offering visitors clear and impactful content wherever excerpts are displayed.

---

- **[Back to the Overview](/overview.md)**
