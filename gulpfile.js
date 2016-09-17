var gulp = require( 'gulp' );

// Requires the gulp-sass plugin
var sass = require( 'gulp-sass' );

var cssbeautify = require( 'gulp-cssbeautify' );

var cssmin = require( 'gulp-cssmin' );

var rename = require( 'gulp-rename' );

var uglify = require( 'gulp-uglify' );

gulp.task( 'sass', function() {
	return gulp.src( 'public/css/sass/**/*.scss' )
		.pipe( sass() ) // Converts Sass to CSS with gulp-sass
		.pipe( cssbeautify() )
		.pipe( cssmin() )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'public/css' ) )
} );

gulp.task( 'minifyjs', function() {
	return gulp.src( 'public/js/bp-activity-share-public.js' )
		.pipe( uglify() )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'public/js' ) )
} );

gulp.task( 'watch', function() {
	gulp.watch( 'public/css/sass/**/*.scss', ['sass'] );
	gulp.watch( 'public/js/bp-activity-share-public.js', ['minifyjs'] );
} );
