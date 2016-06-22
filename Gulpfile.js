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
    npm: './node_modules/',
};

var assets = {
    styles: dir.sass + '**/*.scss',
};

gulp.task('app-sass', function() {
    return gulp.src([
        dir.sass + 'style.scss',
        dir.npm + 'datatables.net-bs/css/dataTables.bootstrap.css',
        ])
        .pipe(sourcemaps.init())
        .pipe(sass(
            // {outputStyle: 'compressed'}
        ).on('error', sass.logError))
        .pipe(concat('style.css'))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(dir.dist));
});

gulp.task('app-js', function() {
    return gulp.src([
        dir.npm + 'jquery/dist/jquery.min.js',
        dir.npm + 'bootstrap-sass/assets/javascripts/bootstrap/modal.js',
        dir.npm + 'datatables.net/js/jquery.dataTables.js',
        dir.npm + 'datatables.net-bs/js/dataTables.bootstrap.js',
        ])
        .pipe(concat('script.js'))
        .pipe(gulp.dest(dir.dist));
});

gulp.task('app-copy-dist', function () {
    gulp.src([
        dir.npm + 'jquery/dist/jquery.min.map'
        ])
        .pipe(gulp.dest(dir.dist));
});

gulp.task('app-copy-fonts', function () {
    gulp.src([
        dir.fonts + '*',
        ])
        .pipe(gulp.dest(dir.dist + 'fonts'));
});

// Rerun the task when a file changes
gulp.task('watch', function () {
    gulp.watch(assets.styles, ['app-sass']);
});

gulp.task('default',
[
    'app-sass',
    'app-copy-fonts',
    'app-js',
    'app-copy-dist',
]);
