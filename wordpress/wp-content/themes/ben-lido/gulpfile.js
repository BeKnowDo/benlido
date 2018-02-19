var gulp = require('gulp'),
  $ = require('gulp-load-plugins')(),
  uglify = require('gulp-uglify'),
  autoprefixer = require('autoprefixer'),
  cleanCSS = require('gulp-clean-css'),
  less = require('gulp-less'),
  concat = require('gulp-concat');

gulp.task('scripts', ['jshint'], function (done) {
    return gulp.src([
        'interactive/javascript/**/*.js'
    ])
        .on('error', function(e) { console.log(e);})
        .pipe(uglify().on('error',function(e) { console.log(e);}))
        .pipe(concat('bl.min.js'))
        .pipe(gulp.dest('js'));
});

gulp.task('jshint', function (done) {
  return gulp.src('interactive/javascript/*.js')
    .on('error', done)
    .pipe($.jshint())
    .pipe($.jshint.reporter('jshint-stylish'));
});

gulp.task('fonts', function(done) {
        return gulp.src([
                    'node_modules/font-awesome/fonts/**'
                ])
                .on('error', done)
                .pipe(gulp.dest('fonts'));
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
            .pipe(concat('bl.libs.css'))
            .pipe(gulp.dest('css'));
});

gulp.task('scripts-libs', function(done) {
	return gulp.src([
		])
        .on('error', done)
        .pipe(uglify().on('error', done))
        .pipe(concat('bl.libs.js'))
        .pipe(gulp.dest('js'));
});

gulp.task('js-lib-no-uglify', function(done) {
	return gulp.src([
        'interactive/js-lib-no-uglify/*.js'
		])
        .on('error', done)
        .pipe(concat('bl.main.js'))
        .pipe(gulp.dest('js'));
});


gulp.task('less', function(done) {
    return gulp.src([
        'node_modules/bootstrap/less/bootstrap.less'
    ])
        .pipe(less({ }))
        .pipe(gulp.dest('css'));
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
    .pipe(gulp.dest('css'))
    .pipe($.sourcemaps.write());

});

gulp.task('default', ['styles-libs','scripts-libs','js-lib-no-uglify','styles', 'scripts','fonts'], function (done) {
  gulp.watch(['interactive/orig/**/*.scss'], ['styles']);
  gulp.watch(['interactive/scss/**/*.scss'], ['styles']);
  gulp.watch(['interactive/javascript/*.js'], ['scripts']);
  gulp.watch(['interactive/js-lib-no-uglify/*.js'], ['js-lib-no-uglify']);
});
