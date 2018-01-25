var gulp = require('gulp'),
  $ = require('gulp-load-plugins')(),
  uglify = require('gulp-uglify'),
  autoprefixer = require('autoprefixer'),
  cleanCSS = require('gulp-clean-css'),
  concat = require('gulp-concat');

gulp.task('scripts', ['jshint'], function (done) {
  return gulp.src([
    'interactive/lib/*.js',
    'interactive/javascript/**/*.js'
  ])
    .on('error', function(e) { console.log(e);})
    .pipe(uglify().on('error',function(e) { console.log(e);}))
    .pipe(concat('benlido.min.js'))
    .pipe(gulp.dest('assets/js'));
});

gulp.task('jshint', function (done) {
  return gulp.src('interactive/javascript/**/*.js')
    .on('error', done)
    .pipe($.jshint())
    .pipe($.jshint.reporter('jshint-stylish'));
});

gulp.task('styles-libs', function(done) {
      return gulp.src([
                'node_modules/animate.css/animate.min.css'
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
              .pipe(concat('blushington.libs.css'))
              .pipe(gulp.dest('assets/css'));
});

gulp.task('styles', [], function (done) {
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

gulp.task('fonts', function(done) {
        return gulp.src([
                        'node_modules/font-awesome/fonts/**'
                ])
                .on('error', done)
                .pipe(gulp.dest('assets/fonts'));
});

gulp.task('default', ['styles-libs','styles', 'scripts','fonts'], function (done) {

  gulp.watch(['interactive/scss/**/*.scss'], ['styles']);
  gulp.watch(['interactive/javascript/**/*.js'], ['scripts']);
  gulp.watch(['interactive/lib/*.js'], ['scripts']);

});
