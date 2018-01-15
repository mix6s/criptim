"use strict";

var gulp = require('gulp'),
	sass = require('gulp-sass'),
	postcss = require('gulp-postcss'),
	autoprefixer = require('autoprefixer'),
	cssnano = require('cssnano'),
	concat = require('gulp-concat'),
	server = require("browser-sync"),
	uglify = require('gulp-uglify'),
	sourcemaps = require('gulp-sourcemaps'),
	imagemin = require('gulp-imagemin'),
	pngquant = require('imagemin-pngquant'),
	del = require('del'),
	rename = require('gulp-rename'),
	runSequence = require('run-sequence').use(gulp);
	//gulpFont = require('gulp-font');

gulp.task('styles', function () {
	var processors = [
		autoprefixer({
			browsers: ['last 2 versions']
		}),
		cssnano({zindex: false})
	];
	return gulp.src('src/Assets/scss/style.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(postcss(processors))
		.pipe(sourcemaps.write())
		.pipe(gulp.dest('web/build/css'))
		.pipe(server.reload({stream: true}));
});
/*
gulp.task('fonts', function () {
    return gulp.src('[\'src/fonts/**//**.+(eot|otf|svg|ttf|woff|woff2)\']')

        .pipe(gulp.dest('web/build/fonts'))
        .pipe(server.reload({stream: true}));
});
*/
gulp.task('images', function () {
	return gulp.src('src/Assets/img/**/*')
		.pipe(imagemin({
			progressive: true,
			svgoPlugins: [{removeViewBox: false}],
			use: [pngquant()]
		}))
		.pipe(gulp.dest('web/build/img'))
		.pipe(server.reload({stream: true}));
});

gulp.task('scripts', function () {
	return gulp.src([
			'bower_components/jquery/dist/jquery.js',
			// 'src/js/jquery-1.8.2.min.js',
			'bower_components/underscore/underscore.js',
			'src/Assets/js/moment.min.js',
			'src/Assets/js/*.js'
		])
		.pipe(concat('scripts.js'))
		.pipe(gulp.dest('web/build/js'))
		.pipe(rename({suffix: '.min'}))
		.pipe(uglify())
		.pipe(gulp.dest('web/build/js'))
		.pipe(server.reload({stream: true}));
});

gulp.task('clean', function () {
	del('web/build/*');
});

gulp.task('build', function(callback) {
	runSequence('clean','styles','scripts','images',callback)
});

gulp.task("serve", ['styles','scripts','images'], function() {
	server.init({
		server: "build",
		notify: false,
		open: true,
		ui: false
	});

	gulp.watch("src/Assets/**/*.{scss,sass}", ["styles"]);
	gulp.watch("src/Assets/**/*.js", ["scripts"]);
	gulp.watch("src/Assets/**/*.+(jpg,png,svg)", ["images"]);
    gulp.watch("src/Assets/**/*.{fonts}", ["fonts"]);
});