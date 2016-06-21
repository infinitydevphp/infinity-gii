/**
 * @author Doniy Serhey <doniysa@gmail.com>
 */

var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');

gulp.task('minapp', function () {
    return gulp.src([
        "./frontend/web/js/origin/*.js",
    ])
        .pipe(uglify({outSourceMap: true}))
        .pipe(rename(function (path) {
            if (path.extname === '.js') {
                path.basename += '.min';
            }
        }))
        .pipe(gulp.dest('./frontend/web/js/min'));
});