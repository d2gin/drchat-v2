const {defineConfig} = require('@vue/cli-service')
module.exports       = defineConfig({
    transpileDependencies: true,
    devServer: {
        https: true,
        proxy: {
            '/api': {
                target: 'http://192.168.1.7',
                ws: true,
                changeOrigin: true,
            },
        },
    },
})
