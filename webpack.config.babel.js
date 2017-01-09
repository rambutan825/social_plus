import webpack from 'webpack'
import path from 'path'
import autoprefixer from 'autoprefixer'
import ExtractTextPlugin from 'extract-text-webpack-plugin'

// 环境变量获取
const NODE_ENV = process.env.NODE_ENV || 'development'

// 是否是正式环境编译
const isProd = NODE_ENV === 'production'

// 各项资源地址定义
const assetsRoot = path.join(__dirname, 'resources', 'assets')
const buildAssetsRoot = path.join(__dirname, 'public')
// const viewAssetsRoot = path.join(__dirname, 'resources', 'views')

// 入口配置
const entry = {
  admin: [
    path.join(assetsRoot, 'admin', 'index.js'),
  ],
}

const cssLoaders = (options = {}) => {
  // generate loader string to be used with extract text plugin
  function generateLoaders (loaders) {
    var sourceLoader = loaders.map(function (loader) {
      var extraParamChar
      if (/\?/.test(loader)) {
        loader = loader.replace(/\?/, '-loader?')
        extraParamChar = '&'
      } else {
        loader = loader + '-loader'
        extraParamChar = '?'
      }
      return loader + (options.sourceMap ? extraParamChar + 'sourceMap' : '')
    }).join('!')

    // Extract CSS when that option is specified
    // (which is the case during production build)
    if (options.extract) {
      return ExtractTextPlugin.extract('vue-style-loader', sourceLoader)
    } else {
      return ['vue-style-loader', sourceLoader].join('!')
    }
  }

  // http://vuejs.github.io/vue-loader/en/configurations/extract-css.html
  return {
    css: generateLoaders(['css']),
    // postcss: generateLoaders(['css']),
    // less: generateLoaders(['css', 'less']),
    // sass: generateLoaders(['css', 'sass?indentedSyntax']),
    // scss: generateLoaders(['css', 'sass']),
    // stylus: generateLoaders(['css', 'stylus']),
    // styl: generateLoaders(['css', 'stylus'])
  }
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
  new webpack.NoErrorsPlugin()
]

const webpackConfig = {
  devtool: isProd ? false : 'source-map',
  entry: entry,
  output: {
    path: path.join(buildAssetsRoot),
    publicPath: '/',
    filename: 'js/[name].js',
  },
  resolve: {
    extensions: ['', '.js', '.vue', '.json'],
    fallback: [path.join(__dirname, 'node_modules')],
    alias: {
      'vue$': 'vue/dist/vue.common.js',
      'admin': path.resolve(assetsRoot, 'admin'),
      'assets': assetsRoot
    }
  },
  resolveLoader: {
    fallback: [path.join(__dirname, 'node_modules')]
  },
  module: {
    loaders: [
      // vue
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },

      // js
      {
        test: /\.js$/,
        loader: 'babel-loader',
        include: [
          assetsRoot
        ],
        exclude: /node_modules/
      },

      // image
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: isProd ? 'images/[hash].[ext]' : `images/[name].[ext]`
        }
      },

      // fonts
      {
        test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
        loader: 'url-loader',
        query: {
          limit: 10000,
          name: isProd ? 'fonts/[hash].[ext]' : `fonts/[name].[ext]`
        }
      },
    ],
  },

  vue: {
    loaders: cssLoaders({
      sourceMap: !isProd,
      extract: true,
    }),
    postcss: [
      autoprefixer({
        browsers: ['last 2 versions']
      })
    ],
  },

  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify(NODE_ENV),
      },
    }),
    new ExtractTextPlugin('css/[name].css'),
    new webpack.optimize.OccurrenceOrderPlugin(),
    // 依托关键加载的插件
    ...plugins,
  ]

}

export default webpackConfig
