'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var path = require('path');
var watch = require('gulp-watch');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');

var dir = {
    fonts: './Resources/fonts/',
    sass: './Resources/sass/',
    js: './Resources/js/',
    dist: './Resources/public/',
    npm: './node_modules/',
};

var assets = {
    styles: dir.sass + '**/*.scss',
};

gulp.task('app-sass', function() {
    return gulp.src([
        dir.sass + 'style.scss',
        dir.npm + 'datatables.net-buttons-bs/css/buttons.bootstrap.css',
        ])
        .pipe(sourcemaps.init())
        .pipe(sass(
            {outputStyle: 'compressed'}
        ).on('error', sass.logError))
        .pipe(concat('style.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(dir.dist));
});

gulp.task('app-js', function() {
    return gulp.src([
            dir.js + 'script.js',
        ])
        .pipe(uglify())
        .pipe(concat('script.js'))
        .pipe(gulp.dest(dir.dist));
});

// Rerun the task when a file changes
gulp.task('watch', function () {
    gulp.watch(assets.styles, ['app-sass', 'app-js']);
});

gulp.task('default',
    [
        'app-sass',
        'app-js',
    ]
);
