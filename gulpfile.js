'use strict';

// Import packages and our package.json so we can use its content.
const gulp = require('gulp'),
      cssnano = require('gulp-cssnano'),
      header = require('gulp-header'),
      rename = require('gulp-rename'),
      uglify = require('gulp-uglify'),
      del = require('del'),
      merge = require('merge-stream'),
      pkg = require('./package.json');

// Banner to be placed on our minified/uglified files
const fileHeader = `/* ${pkg.name} | ${new Date()} */\n`;

// Public paths
const publicCssPath = 'views/public/css',
      publicJsPath = 'views/public/javascripts',
      adminCssPath = 'views/admin/css';

// Minify CSS.
gulp.task('minify', () => {
  const publicCss = gulp.src(`${publicCssPath}/assetrequest.css`)
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(cssnano())
    .pipe(header(fileHeader))
    .pipe(gulp.dest(publicCssPath));

  const adminCss = gulp.src(`${adminCssPath}/assetrequest.css`)
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(cssnano())
    .pipe(header(fileHeader))
    .pipe(gulp.dest(adminCssPath));

  return merge(publicCss, adminCss);
});

// Uglify JS.
gulp.task('uglify', () => {
  return gulp.src(`${publicJsPath}/assetrequest.js`)
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(uglify({'mangle': false}))
    .pipe(header(fileHeader))
    .pipe(gulp.dest(publicJsPath));
});

// Delete generated content.
gulp.task('clean', () => {
  return del.sync(
    [
      `${adminCssPath}/assetrequest.min.css`,
      `${publicCssPath}/assetrequest.min.css`,
      `${publicJsPath}/assetrequest.min.js`
    ]
  );
});

// Do everything.
gulp.task('default', ['clean', 'minify', 'uglify']);
