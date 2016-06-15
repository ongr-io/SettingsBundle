'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var path = require('path');
var watch = require('gulp-watch');
var sourcemaps = require('gulp-sourcemaps');

var dir = {
    fonts: './Resources/fonts/',
    sass: './Resources/sass/',
    dist: './Resources/public/',
};

var assets = {
    styles: dir.sass + '**/*.scss',
};

gulp.task('app-sass', function() {
    return gulp.src(dir.sass + 'style.scss')
        .pipe(sourcemaps.init())
        .pipe(sass(
            // {outputStyle: 'compressed'}
        ).on('error', sass.logError))
        .pipe(concat('style.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(dir.dist));
});

gulp.task('app-copy-fonts', function () {
    gulp.src(dir.fonts + '*')
        .pipe(gulp.dest(dir.dist + 'fonts'));
});

// Rerun the task when a file changes
gulp.task('watch', function () {
    gulp.watch(assets.styles, ['app-sass']);
});

gulp.task('default',
[
    'app-sass',
    'app-copy-fonts'
]);
