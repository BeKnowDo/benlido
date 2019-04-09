var gulp = require('gulp'),
  $ = require('gulp-load-plugins')(),
  uglify = require('gulp-uglify'),
  autoprefixer = require('autoprefixer'),
  cleanCSS = require('gulp-clean-css'),
  concat = require('gulp-concat');

gulp.task('jshint', function (done) {
  return gulp.src('interactive/javascript/*.js')
    .on('error', done)
    .pipe($.jshint())
    .pipe($.jshint.reporter('jshint-stylish'));
});

gulp.task('scripts', gulp.series('jshint', function (done) {
    return gulp.src([
        'interactive/javascript/ui.js',
        'interactive/javascript/main.js'
    ])
        .on('error', function(e) { console.log(e);})
        .pipe(uglify().on('error',function(e) { console.log(e);}))
        .pipe(concat('rethink.min.js'))
        .pipe(gulp.dest('assets/js'));
}));



gulp.task('fonts', function(done) {
        return gulp.src([
                    'node_modules/font-awesome/fonts/**'
                ])
                .on('error', done)
                .pipe(gulp.dest('assets/font-awesome/fonts'));
});

/*
uncomment when we have style libs to gulp
gulp.task('styles-libs', function(done) {
    return gulp.src([
            ])
            .on('error', done)
            .pipe($.postcss([
                    require('postcss-flexibility'),
                    autoprefixer({
                            browsers: ['last 4 versions']
                    })
            ]))
            .pipe(cleanCSS({
                    compatibility: 'ie10'
            }))
            .pipe(concat('benlido.libs.css'))
            .pipe(gulp.dest('assets/css'));
});
*/

/*
uncomment when we have script libs to gulp
gulp.task('scripts-libs', function(done) {
	return gulp.src([
		])
        .on('error', done)
        .pipe(uglify().on('error', done))
        .pipe(concat('benlido.libs.js'))
        .pipe(gulp.dest('assets/js'));
});
*/



gulp.task('styles', function (done) {
  return gulp.src([
    'interactive/scss/style.scss'
  ])
    .on('error', function(e) {console.log(e)})
    .pipe($.sourcemaps.init())
    .on('error', done)
    .pipe($.sass({
      outputStyle: 'nested', // libsass doesn't support expanded yet
      precision: 10,
      includePaths: ['.'],
      onError: console.error.bind(console, 'Sass error:')
    }))
    .pipe($.postcss([
			require('postcss-flexibility'),
			autoprefixer({
				browsers: ['last 4 versions']
			})
		]))
    .pipe($.sourcemaps.write())
    .pipe(concat('style.css'))
    .pipe(gulp.dest('assets/css'))
    .pipe($.sourcemaps.write());

});

//gulp.task('default', gulp.series('styles-libs','scripts-libs','styles','fonts', function (done) {
//  gulp.watch(['interactive/scss/**/*.scss'], ['styles']);
//  gulp.watch(['interactive/javascript/*.js'], ['scripts']);
//}));

gulp.task('watch', function() {
  gulp.watch('interactive/scss/*.scss',gulp.series('styles'));
});

gulp.task('default', gulp.series('styles','watch', function (done) {

  //gulp.watch('interactive/javascript/*.js').on('change', function(path,stats) {
  //  gulp.series('javascript');
  //});
}));
