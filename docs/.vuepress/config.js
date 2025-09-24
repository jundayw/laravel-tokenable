const themeConfig = require('./config/index')

module.exports = {
    base: '/laravel-tokenable/',
    head: [
        ['link', {rel: 'icon', href: '/favicon.ico'}]
    ],
    locales: {
        '/': {
            lang: 'zh-CN',
            title: 'Laravel Tokenable 中文文档',
            description: 'Laravel Tokenable 提供了基于刷新令牌的多模型和跨平台身份验证功能，适用于 API、SPA 和 SSR 应用。'
        },
        '/en/': {
            lang: 'en-US',
            title: 'Laravel Tokenable Documentation',
            description: 'Laravel Tokenable provides multi-model and multi-platform authentication with refresh tokens for APIs, SPAs, and SSR.'
        }
    },
    themeConfig: themeConfig,
    plugins: ['@vuepress/back-to-top'],
    markdown: {
        lineNumbers: true
    }
}
