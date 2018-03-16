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
    return gulp.src('src/Assets/criptim/scss/style.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss(processors))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('build/criptim/css'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-images', function () {
    return gulp.src('src/Assets/criptim/img/**/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest('build/criptim/img'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-layout', function () {
    return gulp.src(
        [
            'src/Assets/criptim/njc/pages/**/*.+(html|njc)'
        ]
    )
        .pipe(nunjucksRender())
        .pipe(gulp.dest('build/criptim'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-scripts', function () {
    return gulp.src([
        'bower_components/jquery/dist/jquery.js',
        'bower_components/underscore/underscore.js',
        'src/Assets/criptim/js/*.js'
    ])
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest('build/criptim/js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('build/criptim/js'))
        .pipe(server.reload({stream: true}));
});

gulp.task('build-clean', function () {
    del('build/criptim*');
});
gulp.task('web-clean', function () {
    del('web/build/criptim*');
});

gulp.task('web-copy-css', function () {
    return gulp.src(['build/criptim/css/*'])
        .pipe(gulp.dest('web/build/criptim/css'));
});
gulp.task('web-copy-img', function () {
    return gulp.src(['build/criptim/img/*'])
        .pipe(gulp.dest('web/build/criptim/img'));
});
gulp.task('web-copy-js', function () {
    return gulp.src(['build/criptim/js/*'])
        .pipe(gulp.dest('web/build/criptim/js'));
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
            server: "build/criptim",
            notify: false,
            open: true,
            ui: false
        });

        gulp.watch("src/Assets/criptim/**/*.+(html|njc)", ["build-layout"]);
        gulp.watch("src/Assets/criptim/**/*.{scss,sass}", ["build-styles"]);
        gulp.watch("src/Assets/criptim/**/*.js", ["build-scripts"]);
        gulp.watch("src/Assets/criptim/**/*.+(jpg,png,svg)", ["build-images"]);
    }
);


gulp.task('default', ['build']);