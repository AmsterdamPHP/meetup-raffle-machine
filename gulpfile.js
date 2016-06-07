var gulp = require('gulp'),
    plumber = require('gulp-plumber'),
    rename = require('gulp-rename');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var imagemin = require('gulp-imagemin'),
    cache = require('gulp-cache');
var minifycss = require('gulp-minify-css');
var sass = require('gulp-sass');
var sassIncl = require('sass-include-paths');

gulp.task('images', function(){
    gulp.src('source/images/**/*')
        .pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
        .pipe(gulp.dest('web/img/'));
});

gulp.task('styles', function(){
    gulp.src(['source/scss/**/*.scss'])
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }}))
        .pipe(sass({
            includePaths: sassIncl.nodeModulesSync()
        }))
        .pipe(autoprefixer('last 2 versions'))
        .pipe(gulp.dest('web/css/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest('web/css/'))
});

gulp.task('scripts', function(){
    return gulp.src('source/js/**/*.js')
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }}))
        .pipe(concat('main.js'))
        .pipe(gulp.dest('web/js/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('web/js/'))
});

gulp.task('default', ['images', 'styles', 'scripts']);

gulp.task('watch', function(){
    gulp.watch("source/scss/**/*.scss", ['styles']);
    gulp.watch("source/js/**/*.js", ['scripts']);
});
