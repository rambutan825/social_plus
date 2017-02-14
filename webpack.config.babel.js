import webpack from 'webpack';
import path from 'path';
import autoprefixer from 'autoprefixer';
import ExtractTextPlugin from 'extract-text-webpack-plugin';
import { StatsWriterPlugin } from 'webpack-stats-plugin';
import lodash from 'lodash';
import fs from 'fs';

// 环境变量获取
const NODE_ENV = process.env.NODE_ENV || 'development';
const isHot = process.argv.includes('--hot');

// 是否是正式环境编译
const isProd = NODE_ENV === 'production'

// 各项资源地址定义
const assetsRoot = path.join(__dirname, 'resources', 'assets');
const buildAssetsRoot = path.resolve(__dirname, 'public/');

// 入口配置
const entry = {
  admin: path.join(assetsRoot, 'admin', 'index.js'),
}

const cssLoaders = (options = {}) => {
  // generate loader string to be used with extract text plugin
  function generateLoaders (loaders) {
    var sourceLoader = loaders.map(function (loader) {
      var extraParamChar;
      if (/\?/.test(loader)) {
        loader = loader.replace(/\?/, '-loader?')
        extraParamChar = '&'
      } else {
        loader = loader + '-loader'
        extraParamChar = '?'
      }
      return loader + (options.sourceMap ? extraParamChar + 'sourceMap' : '')
    }).join('!');

    // Extract CSS when that option is specified
    // (which is the case during production build)
    if (options.extract) {
      return ExtractTextPlugin.extract({
        use: sourceLoader,
        fallback: 'vue-style-loader'
      });
    } else {
      return ['vue-style-loader', sourceLoader].join('!')
    }
  }

  // http://vuejs.github.io/vue-loader/en/configurations/extract-css.html
  return {
    css: generateLoaders(['css']),
    postcss: generateLoaders(['css']),
    // less: generateLoaders(['css', 'less']),
    sass: generateLoaders(['css', 'sass?indentedSyntax']),
    scss: generateLoaders(['css', 'sass']),
    // stylus: generateLoaders(['css', 'stylus']),
    // styl: generateLoaders(['css', 'stylus'])
  }
};

// Generate loaders for standalone style files (outside of .vue)
// const styleLoaders = (options) => {
//   let output = []
//   let loaders = cssLoaders(options)
//   for (let extension in loaders) {
//     let loader = loaders[extension]
//     output.push({
//       test: new RegExp('\\.' + extension + '$'),
//       loader: loader
//     })
//   };
//   return output
// };

function MixManifest(stats) {
  let flattenedPaths = [].concat.apply([], lodash.values(stats.assetsByChunkName));

  let manifest = flattenedPaths.reduce((manifest, filename) => {
    let original = filename.replace(/\.(\w{20})(\..+)/, '$2');
    manifest['/'+original] = '/'+filename;
    // manifest[original] = filename;

    return manifest;
  }, {});

  // return stats;
  return JSON.stringify(manifest, null, 2);
}

// 环境插件～不同环境启用的不同插件.
const plugins = isProd ?
[
  new webpack.optimize.UglifyJsPlugin({
    compress: {
      warnings: false
    }
  })
] : 
[
  // https://github.com/glenjamin/webpack-hot-middleware#installation--usage
  new webpack.NoEmitOnErrorsPlugin(),
];

const webpackConfig = {
  devtool: isProd ? false : 'source-map',
  entry: entry,
  output: {
    path: isHot ? '/' : path.join(buildAssetsRoot),
    publicPath: isHot ? 'http://localhost:8080/' : '../',
    filename: isProd ? 'js/[name].[chunkhash].js' : 'js/[name].js',
  },
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    modules: [
      assetsRoot,
      path.join(__dirname, 'node_modules'),
    ],
    alias: {
      'jquery': 'jquery/dist/jquery.js',
      'vue$': 'vue/dist/vue.common.js'
    }
  },
  module: {
    rules: [
      // ...styleLoaders({
      //   sourceMap: !isProd,
      //   extract: true,
      // }),
      {
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        enforce: "pre",
        include: [
          assetsRoot,
        ]
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: cssLoaders({
            sourceMap: !isProd,
            extract: true,
          }),
          postcss: [
            autoprefixer({
              browsers: [
                'Android 2.3',
                'Android >= 4',
                'Chrome >= 20',
                'Firefox >= 24',
                'Explorer >= 8',
                'iOS >= 6',
                'Opera >= 12',
                'Safari >= 6'
              ]
            })
          ]
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        include: [assetsRoot]
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: isProd ? 'images/[hash].[ext]' : `images/[name].[ext]`
        }
      },
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: isProd ? 'fonts/[hash].[ext]' : `fonts/[name].[ext]`
        }
      }
    ]
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify(NODE_ENV),
      },
    }),
    // extract css into its own file
    new ExtractTextPlugin(isProd ? 'css/[name].[chunkhash].css' : 'css/[name].css'),
    new webpack.optimize.OccurrenceOrderPlugin(),
    new StatsWriterPlugin({
      filename: 'mix-manifest.json',
      transform: MixManifest
    }),
    // 依托关键加载的插件
    ...plugins,
  ],

  // Webpack Dev Server Configuration.
  devServer: {
    historyApiFallback: true,
    noInfo: true,
    compress: true
  }

};

// mix.
if (isHot) {

  // hot file.
  let hotFile = buildAssetsRoot+'/hot';
  if (fs.existsSync(hotFile)) {
    fs.unlinkSync(hotFile);
  }
  // hot reloading enabled
  fs.writeFileSync(hotFile, 'hot reloading enabled');
}

export default webpackConfig;
