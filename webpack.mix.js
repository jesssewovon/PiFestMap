const mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 | How To Install Vue 3 in Laravel 10 From Scratch
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.webpackConfig(webpack => {
	return {
		resolve: {
			alias: {
				videojs: 'video.js',
				WaveSurfer: 'wavesurfer.js',
				RecordRTC: 'recordrtc'
			}
		},
		plugins: [
			new webpack.ProvidePlugin({
				videojs: 'video.js/dist/video.cjs.js',
				RecordRTC: 'recordrtc'
			})
		]
	}
})

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .postCss('resources/css/app.css', 'public/css', [
        //
    ])
    .version();