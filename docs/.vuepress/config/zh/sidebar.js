module.exports = [
    {
        title: '配置',
        collapsable: false,
        children: [
            '/configuration/token',
            '/configuration/token-management',
            '/configuration/token-suspension',
            '/configuration/token-ttl',
            '/configuration/token-driver',
            '/configuration/token-database',
            '/configuration/token-queue',
            '/configuration/token-cache',
        ]
    },
    {
        title: '基础',
        collapsable: false,
        children: [
            '/usage/introduction',
            '/usage/quick-start',
            '/usage/create-token',
            '/usage/refresh-token',
            '/usage/revoke-token',
            '/usage/auth-code',
            '/usage/suspend-token',
        ]
    },
    {
        title: '指南',
        collapsable: false,
        children: [
            'guide/header',
        ]
    },
    {
        title: '最佳实践',
        collapsable: false,
        children: [
            '/features/example',
            '/features/cookie',
        ]
    }
]
