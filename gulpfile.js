const gulp = require('gulp')
const { task, series, parallel } = gulp
const browserSync = require('browser-sync').create()
const sass = require('gulp-sass')
sass.compiler = require('node-sass')

const PROXY_PORT = process.env.PROXY_PORT || 3000
const PORT = process.env.PORT || 8080

const watch = {
  js: 'js/**/*.js',
  sass: 'sass/**/*.scss',
  php: 'includes/**/*.php',
  twig: 'includes/tempaltes/**/*.html'
}

const sources = {
  sass: 'sass/styles.scss'
}

const dist = {
  sass: 'css'
}

task('serve', done => {
  browserSync.init({
    proxy: `localhost:${PROXY_PORT}`,
    port: PORT,
    open: false,
    notify: false,
  })
  done()
})

task('reload', done => {
  browserSync.reload()
  done()
})

task('sass:build', () => {
  return gulp.src(sources.sass)
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest(dist.sass));
});

task('js:watch', done => {
  gulp.watch(watch.js, series([
    'reload',
  ]))
  done()
})

task('php:watch', done => {
  gulp.watch(watch.php, series([
    'reload',
  ]))
  done()
})

task('sass:watch', done => {
  gulp.watch(watch.sass, series([
    'sass:build',
    'reload',
  ]))
  done()
})

task('twig:watch', done => {
  gulp.watch(watch.twig, series([
    'reload',
  ]))
  done()
})

task(
  'default',
  series([
    'sass:build',
    parallel([
      'js:watch',
      'php:watch',
      'sass:watch',
      'twig:watch',
    ]),
    'serve',
  ])
)