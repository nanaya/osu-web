// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

'use strict';

//#region plugin imports
const Autoprefixer = require('autoprefixer');
const CopyPlugin = require('copy-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const SentryPlugin = require('webpack-sentry-plugin');
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');

//#endregion

// #region non-plugin imports
const fs = require('fs');
const path = require('path');
const webpack = require('webpack');

//#endregion

//#region env
const inProduction = process.env.NODE_ENV === 'production' || process.argv.includes('-p');
const paymentSandbox = !(process.env.PAYMENT_SANDBOX === '0'
                         || process.env.PAYMENT_SANDBOX === 'false'
                         || !process.env.PAYMENT_SANDBOX);

//#endregion

//#region helpers
function resolvePath(...segments) {
  return path.resolve(__dirname, ...segments);
}

//#endregion

//#region entrypoints and output
const entry = {
  'app': [
    './resources/assets/lib/app.js',
    './resources/assets/less/app.less',
  ],
};

const coffeeReactComponents = [
  'artist-page',
  'beatmap-discussions',
  'beatmap-discussions-history',
  'beatmapset-page',
  'changelog-build',
  'changelog-index',
  'comments-index',
  'comments-show',
  'mp-history',
  'modding-profile',
  'profile-page',
  'admin/contest',
  'contest-entry',
  'contest-voting',
];

const tsReactComponents = [
  'account-edit',
  'beatmaps',
  'chat',
  'friends-index',
  'groups-show',
  'news-index',
  'news-show',
  'notifications-index',
  'scores-show',
  'store-bootstrap',
];

for (const name of coffeeReactComponents) {
  entry[`react/${name}`] = [resolvePath(`resources/assets/coffee/react/${name}.coffee`)];
}

for (const name of tsReactComponents) {
  entry[`react/${name}`] = [resolvePath(`resources/assets/lib/${name}.ts`)];
}

const output = {
  filename: 'js/[name].[contenthash].js',
  path: resolvePath('public/assets'),
  publicPath: '/assets/',
};

//#endregion

//#region plugin list
const plugins = [
  new webpack.ProvidePlugin({
    $: 'jquery',
    _: 'lodash',
    Cookies: 'js-cookie',
    d3: 'd3', // TODO: d3 is fat and probably should have it's own chunk
    jQuery: 'jquery',
    moment: 'moment',
    React: 'react',
    ReactDOM: 'react-dom',
    Turbolinks: 'turbolinks',
  }),
  new webpack.DefinePlugin({
    'process.env.DOCS_URL': JSON.stringify(process.env.DOCS_URL || 'https://docs.ppy.sh'),
    'process.env.PAYMENT_SANDBOX': JSON.stringify(paymentSandbox),
    'process.env.SHOPIFY_DOMAIN': JSON.stringify(process.env.SHOPIFY_DOMAIN),
    'process.env.SHOPIFY_STOREFRONT_TOKEN': JSON.stringify(process.env.SHOPIFY_STOREFRONT_TOKEN),
  }),
  new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/), // don't add moment locales to bundle.
  new MiniCssExtractPlugin({
    filename: 'css/[name].[contenthash].css',
  }),
  new CopyPlugin({
    patterns: [
      { from: 'resources/assets/build/locales', to: 'copy/locales/[name].[contenthash].[ext]' },
      { from: 'node_modules/moment/locale', to: 'copy/moment-locales/[name].[contenthash].[ext]' },
    ],
  }),
  new ManifestPlugin({
    map: (file) => {
      // Workaround for CopyPlugin so we don't need to know the file hashes beforehand
      // when using the manifest.
      //
      // Reference: https://github.com/webpack-contrib/copy-webpack-plugin/issues/104
      file.name = file.name.replace(/^(copy\/.*\.)[a-f0-9]{32}\.([^.]+)$/, '$1$2');

      return file;
    },
  }),
];

if (process.env.SENTRY_RELEASE === '1') {
  plugins.push(
    new SentryPlugin({
      apiKey: process.env.SENTRY_API_KEY,
      organisation: process.env.SENTRY_ORG,
      project: process.env.SENTRY_PROJ,

      deleteAfterCompile: true,
      exclude: /\.css(\.map)?$/,
      filenameTransform: function(filename) {
        return '~' + filename;
      },
      release: function() {
        return process.env.GIT_SHA;
      },
    }),
  );
}

//#endregion

//#region Loader rules
const rules = [
  {
    enforce: 'pre',
    exclude: /(node_modules)/,
    loader: 'import-glob-loader',
    test: /\.(js|coffee)$/,
  },
  {
    exclude: /(node_modules|bower_components)/,
    test: /\.jsx?$/,
    use: [
      {
        loader: 'babel-loader',
        options: {
          cacheDirectory: true,
          plugins: [
            '@babel/plugin-syntax-dynamic-import',
            '@babel/plugin-proposal-object-rest-spread',
            [
              '@babel/plugin-transform-runtime',
              {
                helpers: false,
              },
            ],
          ],
          presets: [
            [
              '@babel/preset-env',
              {
                forceAllTransforms: true,
                modules: false,
              },
            ],
          ],
        },
      },
    ],
  },
  {
    exclude: /node_modules/,
    loader: 'ts-loader',
    test: /\.tsx?$/,
  },
  {
    // loader for preexisting global coffeescript
    exclude: [
      resolvePath('resources/assets/coffee/react'),
    ],
    include: [
      resolvePath('resources/assets/coffee'),
    ],
    test: /\.coffee$/,
    use: ['imports-loader?jQuery=jquery,$=jquery,this=>window', 'coffee-loader'],
  },
  {
    // loader for import-based coffeescript
    include: [
      resolvePath('resources/assets/coffee/react'),
      resolvePath('resources/assets/lib'),
    ],
    test: /\.coffee$/,
    use: ['coffee-loader'],
  },
  {
    test: /\.less$/,
    use: [
      MiniCssExtractPlugin.loader,
      {
        loader: 'css-loader',
        options: {
          importLoaders: 1,
          sourceMap: true,
          // url-loader didn't try to resolve aboslute paths before 4.1
          // https://github.com/webpack-contrib/css-loader/commit/f9ba0ce11789770c4c9220478e9c98dbd432a5d6
          url: (url) => !url.startsWith('/'),
        },
      },
      {
        loader: 'postcss-loader',
        options: {
          plugins: [Autoprefixer],
          sourceMap: true,
        },
      },
      { loader: 'less-loader', options: { sourceMap: true } },
    ],
  },
  {
    loader: 'file-loader',
    options: {
      name: 'images/[name].[contenthash].[ext]',
    },
    test: /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/,
  },
  {
    loader: 'file-loader',
    options: {
      name: 'fonts/[name].[contenthash].[ext]',
    },
    test: /(\.(woff2?|ttf|eot|otf)$|font.*\.svg$)/,
  },
];

//#endregion

//#region resolvers
const resolve = {
  alias: {
    'layzr': resolvePath('node_modules/layzr.js/dist/layzr.module.js'),
    'ziggy': resolvePath('resources/assets/js/ziggy.js'),
    'ziggy-route': resolvePath('vendor/tightenco/ziggy/dist/js/route.js'),
  },
  extensions: ['*', '.js', '.coffee', '.ts', '.tsx'],
  modules: [
    resolvePath('resources/assets/coffee'),
    resolvePath('resources/assets/lib'),
    resolvePath('resources/assets/coffee/react/_components'),
    resolvePath('node_modules'),
  ],
  plugins: [new TsconfigPathsPlugin()],
};

//#endregion

//#region optimization and chunk splitting settings
const cacheGroups = {
  commons: {
    chunks: 'initial',
    minChunks: 2,
    name: 'commons',
    priority: -20,
  },
  vendor: {
    chunks: 'initial',
    name: 'vendor',
    priority: -10,
    reuseExistingChunk: true,
    test: (module, chunks) => {
      // Doing it this way doesn't split the css imported via app.less from the main css bundle.
      return module.resource && module.resource.includes(`${path.sep}node_modules${path.sep}`);
    },
  },
};

const optimization = {
  moduleIds: 'hashed',
  runtimeChunk: {
    name: 'runtime',
  },
  splitChunks: {
    cacheGroups,
  },

};

if (inProduction) {
  optimization.minimizer = [
    new TerserPlugin({
      sourceMap: true,
      terserOptions: {
        safari10: true,
      },
    }),
    new CssMinimizerPlugin({
      sourceMap: true,
    }),
  ];
}

//#endregion

module.exports = {
  devtool: 'source-map',
  entry,
  mode: inProduction ? 'production' : 'development',
  module: {
    rules,
  },
  optimization,
  output,
  plugins,
  resolve,
  stats: {
    entrypoints: false,
    errorDetails: false,
    excludeAssets: [
      // exclude copied files
      /^js\/locales\//,
      /^\/fonts\//,
      /^vendor\//,
    ],
  },
};
