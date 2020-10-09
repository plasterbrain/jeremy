/**
 * Gulp File.
 *
 * @file Defines gulp tasks for the theme.
 * @version 2.0.0
 * @license GPL-3.0-or-later
 */

var gulp = require( 'gulp' ),
	autoprefixer = require( 'autoprefixer' ),
	//browserSync = require( 'browser-sync' ).create(),
	postcss = require( 'gulp-postcss' ),
	readme = require( 'gulp-readme-to-markdown' ),
	rename = require( 'gulp-rename' ),
	rtlcss = require( 'gulp-rtlcss' ),
	sass = require( 'gulp-sass' ),
	uglify = require( 'gulp-uglify' ),
	wait = require( 'gulp-wait' ),

	scss = 'assets/sass/',
	css = 'assets/css/',
	js = 'assets/js/';

gulp.task( 'css', function() {
	return (
		gulp.src( scss + 'style.scss' )
			.pipe( wait( 100 ) ) // Fix Sass error bug
			.pipe( sass( { // Compile Sass
				outputStyle: 'compressed',
			} ) ).on( 'error', sass.logError )
			.pipe( postcss( [ // Add browser prefixes
				autoprefixer({
					browsers: ['last 2 versions', '> 1%', 'not ie <= 11'],
					grid: 'true'
				} ),
			] ) )
			.pipe( rename( 'style.min.css' ) )
			.pipe( gulp.dest( css ) )
			.pipe( rtlcss() ) // Create RTL stylesheet
			.pipe( rename( 'style-rtl.min.css' ) )
			.pipe( gulp.dest( css ) )
	);
} );

gulp.task( 'js', function() {
	return (
		gulp.src( js + '*.js' )
			.pipe( uglify() )
			.pipe( rename( { suffix: '.min' } ) )
			.pipe( gulp.dest( js + '/min/' ) )
	);
} );

gulp.task( 'readme', function() {
  gulp.src( 'readme.txt' )
  .pipe( readme( {} ) )
  .pipe( gulp.dest( '.' ) );
} );

gulp.task( 'watch', function() {
	/*
	browserSync.init({
		open: 'external',
		proxy: 'presto.test',
		port: 8080
	});
	*/
	gulp.watch( scss + '**/*.scss', gulp.series( 'css' ) );
	gulp.watch( js + '*.js', gulp.series( 'js' ) );
	gulp.watch( 'readme.txt', gulp.series( 'readme' ) );
} );

gulp.task( 'default', gulp.series( 'watch' ) );
