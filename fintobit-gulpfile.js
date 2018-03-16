"use strict";

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    nunjucksRender = require('gulp-nunjucks-render'),
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

gulp.task('build-styles', function () {
    var processors = [
        autoprefixer({
            browsers: ['last 2 versions']
        }),
        cssnano({zindex: false})
    ];
    return gulp.src('src/Assets/fintobit/scss/style.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss(processors))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('build/fintobit/css'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-images', function () {
    return gulp.src('src/Assets/fintobit/img/**/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('build/fintobit/img'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-layout', function () {
    return gulp.src(
        [
            'src/Assets/fintobit/njc/pages/**/*.+(html|njc)'
        ]
    )
        .pipe(nunjucksRender())
        .pipe(gulp.dest('build/fintobit'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-scripts', function () {
    return gulp.src([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/underscore/underscore.js',
        'src/Assets/fintobit/js/*.js'
    ])
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest('build/fintobit/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('build/fintobit/js'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-clean', function () {
    del('build/fintobit*');
});
gulp.task('web-clean', function () {
    del('web/build/fintobit*');
});

gulp.task('web-copy-css', function () {
    return gulp.src(['build/fintobit/css/*'])
        .pipe(gulp.dest('web/build/fintobit/css'));
});
gulp.task('web-copy-img', function () {
    return gulp.src(['build/fintobit/img/*'])
        .pipe(gulp.dest('web/build/fintobit/img'));
});
gulp.task('web-copy-js', function () {
    return gulp.src(['build/fintobit/js/*'])
        .pipe(gulp.dest('web/build/fintobit/js'));
});

gulp.task('web-copy', function (callback) {
    runSequence(
        'web-copy-css',
        'web-copy-img',
        'web-copy-js',
        callback
    )
});

gulp.task(
    'build',
    function (callback) {
        runSequence(
            'build-clean',
            'build-styles',
            'build-scripts',
            'build-images',
            'build-layout',
            'web-clean',
            'web-copy',
            callback
        )
    }
);

gulp.task(
    "serve",
    [
        'build-styles',
        'build-scripts',
        'build-images',
        'build-layout',
    ],
    function () {
        server.init({
            server: "build/fintobit",
            notify: false,
            open: true,
            ui: false
        });

        gulp.watch("src/Assets/fintobit/**/*.+(html|njc)", ["build-layout"]);
        gulp.watch("src/Assets/fintobit/**/*.{scss,sass}", ["build-styles"]);
        gulp.watch("src/Assets/fintobit/**/*.js", ["build-scripts"]);
        gulp.watch("src/Assets/fintobit/**/*.+(jpg,png,svg)", ["build-images"]);
    }
);


gulp.task('default', ['build']);