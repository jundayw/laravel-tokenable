const zhConfig = require('./zh')
const enConfig = require('./en')

module.exports = {
    logo: '/logo.svg',
    repo: 'jundayw/laravel-tokenable',
    docsDir: 'docs',
    docsBranch: 'docs',
    editLinks: true,

    locales: {
        '/': {
            selectText: '选择语言',
            label: '简体中文',
            editLinkText: '在 GitHub 上编辑此页',
            serviceWorker: {
                updatePopup: {
                    message: "发现新内容可用.",
                    buttonText: "刷新"
                }
            },

            nav: zhConfig.navbar,
            sidebar: zhConfig.sidebar,
        },
        '/en/': {
            selectText: 'Languages',
            label: 'English',
            ariaLabel: 'Languages',
            editLinkText: 'Edit this page on GitHub',
            serviceWorker: {
                updatePopup: {
                    message: "New content is available.",
                    buttonText: "Refresh"
                }
            },

            nav: enConfig.navbar,
            sidebar: enConfig.sidebar,
        }
    },
    nextLinks: true,
    prevLinks: true,
}
