# Getting Started

**Kognetiks AI Summaries** is a plugin that allows you to effortlessly integrate AI generated summaries of your pages and posts into your website, providing  powerful, AI-driven content for enhanced user experience and personalized support.

AI platforms - like those from OpenAI, NVIDIA, Anthropic, and others - use natural language processing and machine learning algorithms to interact with users in a human-like manner. They are designed to answer questions, provide suggestions, and engage in conversations with users. One of their outstanding capabilities is to ingest information and generate concise summaries.

The **Kognetiks AI Summaries** plugin is powered by OpenAI, NVIDIA, Anthropic, DeepSeek, Mistral, Google, and Local AI servers via their APIs and Large Language Models (LLMs) to bring artificial intelligence to life within your WordPress website.

**Important Note:** This plugin requires an API key from one of the supported AI platforms to function correctly. You can obtain an API key by signing up with one of the vendors below:

- [OpenAI](https://platform.openai.com/account/api-keys)

- [NVIDIA](https://developer.nvidia.com/nim)

- [Anthropic](https://www.anthropic.com/)

- [DeepSeek](https://platform.deepseek.com/sign_in)

- [Mistral](https://console.mistral.ai/api-keys)

- [Google](https://aistudio.google.com/api-keys)

- [Local](https://www.jan.ai/docs/desktop/api-server)

## Steps

1. From the `General` tab on the **Kognetiks AI Summaries** settings page, select from one of seven AI Platform Choices: `OpenAI` (the default), `NVIDIA`, `Anthropic`, `DeepSeek`, `Mistral`, `Google`, or `Local`.

2. Before you can `Turn AI Summaries` to `On`, you'll need to set up an account with the AI Platform vendor of your choice.  The plugin is very efficient when it comes to generating AI excerpts of your content.

    - The plugin will only generate a summary of a page, post or other published content once.  But, it will check to see if the content has been updated or modified.  If the page or post has changed since the last time the summary was generated, a new summary will be created.  This makes incorporating AI generated excerpts very cost affordable.
    - AI generated excerpts appear where ever WordPress would present an excerpt on your site.  It **does not** replace any hand-crafted excerpts you might have added to posts, pages or other content.  AI summaries are in addition to these excerpts and are stored in a separate table on your site.  Once generated, they won't be regenerated unless the content has been updated.

3. From the appropriate API tab (`API/OpenAI`, `API/NVIDIA`, `API/Anthropic`, `API/DeepSeek`, `API/Mistral`, `API/Google`, or `API/Local`) depending on the `AI Platform Choice` you made on the `General` tab, enter your `API Key`, then click the `Save Settings` button at the bottom of the page.

4. Once you have saved your API key, the list of currently available models from the AI Platform vendor of your choice will be available in the `Model Choice` pull down menu.  In most cases, the `Model Choice` will default to the latest model.  If you changes models, remember to click the `Save Settings` button at the bottom of the page.

5. In most cases you should not need to change the `Advanced Settings` defaults.

6. You can check that everything is working as expected by clicking on the `Diagnostics` tabs.  Once the page has loaded, the `API STATUS` will reflect `Success: Connection to the [Selected Platform] API was successful!`.  If there is a problem with the API service or your account, a more detailed error message will be presented.

7. Now that you have configured the plugin, return to the `General` tab, and `Turn AI Summaries On/Off` by selecting `On`.  Then choose the `AI Summaries Length (Words)` from the pull down (the default is a brief summary of only 55 words).  Remember to click the `Save Settings` button at the bottom of the page.

8. Now you're ready to generate summaries.  Use any page on your site where a summary might appear, such as `Search`, to see an automatically crafted, concise, and insightful summarization of your content. These polished excerpts seamlessly enhance your site, offering visitors clear and impactful content wherever excerpts are displayed.

---

- **[Back to the Overview](/overview.md)**
