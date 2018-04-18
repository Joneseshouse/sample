const webpack = require('webpack');
// const NpmInstallPlugin = require('npm-install-webpack-plugin');
const merge = require('webpack-merge');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const CompressionPlugin = require('compression-webpack-plugin');
const DuplicatePackageCheckerPlugin = require('duplicate-package-checker-webpack-plugin');
// const PrepackWebpackPlugin = require('prepack-webpack-plugin').default;

// const configuration = {};

const TARGET = process.env.npm_lifecycle_event;
const path = require('path');
const fs = require('fs');
const PATHS = {
    app: path.join(__dirname, 'app'),
    build: path.join(__dirname, 'build'),
    test: path.join(__dirname, 'tests'),
};

process.env.BABEL_ENV = TARGET;

const common = {
    entry: {
        app: PATHS.app,
    },
    resolve: {
        modules: [path.resolve(__dirname), 'node_modules'],
        alias: {
            app: 'app',
            libs: 'app/libs/',
            components: 'app/components/',
            utils: 'app/utils/',
            images: 'app/images/',
            helpers: 'app/utils/helpers',
        },
        extensions: ['.js', '.jsx'],
    },
    output: {
        path: PATHS.build,
        // publicPath: '/build/',
        filename: '[name].js',
    },
    module: {
        loaders: [
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.scss$/,
                use: ['style-loader', 'css-loader', 'sass-loader'],
            },
            {
                test: /\.less$/,
                use: ['style-loader', 'css-loader', 'less-loader'],
            },
            {
                test: /\.styl$/,
                use: ['style-loader', 'css-loader', 'stylus-loader'],
            },
            {
                test: /\.png$/,
                // loader: "url-loader?limit=100000"
                loader: 'file-loader',
            },
            {
                test: /\.jpg$/,
                // loader: "url-loader?limit=100000"
                loader: 'file-loader',
            },
            {
                test: /\.gif$/,
                // loader: "url-loader?limit=100000"
                loader: 'file-loader',
            },
            {
                test: /\.woff($|\?)|\.woff2($|\?)|\.ttf($|\?)|\.eot($|\?)|\.svg($|\?)/,
                // loader: 'url-loader'
                loader: 'file-loader',
            },
            {
                test: /\.jsx?$/,
                loader: 'babel-loader',
                include: PATHS.app,
                exclude: /node_modules/,
            },
        ],
    },
};

if (TARGET === 'start' || !TARGET) {
    module.exports = merge(common, {
        watchOptions: {
            ignored: /node_modules/,
        },
        devtool: 'eval-source-map',
        // devtool: 'eval',
        devServer: {
            disableHostCheck: true,
            contentBase: PATHS.build,
            historyApiFallback: true,
            hot: true,
            stats: 'errors-only',
            host: '0.0.0.0',
            port: 4004,
            https: {
                cert: fs.readFileSync('/code/ssl/cer.crt'),
                key: fs.readFileSync('/code/ssl/rsa.key'),
                ca: fs.readFileSync('/code/ssl/localca.pem'),
            },
        },
        plugins: [new webpack.NamedModulesPlugin()],
    });
}

if (['build', 'buildLocal', 'deploy', 'deployPure', 'deploy24', 'deploy24Pure', 'stats'].indexOf(TARGET) !== -1) {
    module.exports = merge(common, {
        devtool: 'cheap-module-source-map',
        /*
        performance: {
            hints: 'warning', // 'error' or false are valid too
            maxEntrypointSize: 100000, // in bytes
            maxAssetSize: 200000 // in bytes
        },
        */
        plugins: [
            new webpack.DefinePlugin({
                'process.env': {
                    NODE_ENV: JSON.stringify('production'),
                },
            }),
            // new PrepackWebpackPlugin(configuration),
            // new BundleAnalyzerPlugin(),
            new webpack.optimize.UglifyJsPlugin({
                sourceMap: true,
                mangle: true,
                compress: {
                    warnings: false, // Suppress uglification warnings
                    pure_getters: true,
                    unsafe: true,
                    unsafe_comps: true,
                    screw_ie8: true,
                },
                output: {
                    comments: false,
                },
                exclude: [/\.min\.js$/gi], // skip pre-minified libs
            }),
            // new webpack.IgnorePlugin(/^\.\/locale$/, [/moment$/]),
            new webpack.NoEmitOnErrorsPlugin(),
            new CompressionPlugin({
                asset: '[path].gz[query]',
                algorithm: 'gzip',
                test: /\.js$|\.css$|\.html$/,
                threshold: 10240,
                minRatio: 0,
            }),
            /*
            new webpack.ProvidePlugin({
                $: "jquery",
                jQuery: "jquery",
                'window.jQuery': 'jquery'
            })
            */
        ],
        stats: {colors: true},
    });
}

if (TARGET === 'test' || TARGET === 'tdd') {
    module.exports = merge(common, {
        devtool: 'inline-source-map',
        resolve: {
            alias: {
                app: PATHS.app,
            },
        },
        module: {
            /*
            rules: [{
                test: /\.jsx?$/,
                loaders: ['isparta-instrumenter'],
                enforce: 'pre',
                include: PATHS.app
            }],
            */
            loaders: [
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    include: [path.join(__dirname, 'app'), path.join(__dirname, 'tests')],
                    exclude: path.join(__dirname, 'node_modules'),
                },
                {
                    test: /\.jsx$/,
                    loader: 'babel-loader',
                    include: [path.join(__dirname, 'app'), path.join(__dirname, 'tests')],
                    exclude: path.join(__dirname, 'node_modules'),
                },
            ],
        },
    });
}
// "build": "webpack && scp -r build/* tbson@leader_server:/home/tbson/nginx/rdathang_api/public/clients/source/",
