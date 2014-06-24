var os = require('os');

module.exports = function(grunt) {
    grunt.loadTasks('grunt/tasks');

    var path = grunt.option('path');
    path = path && path.replace(/\/+$/, '') + '/' || os.tmpdir();

    grunt.initConfig({
        karma: {
            options: {
                assetsDir: 'grunt/assets',
                autoWatch: false,
                browsers: ['PhantomJS'],
                configFile: 'grunt/karma.conf.js',
                singleRun: true
            },
            dev: {
                autoWatch: true,
                browsers: ['Chrome'],
                singleRun: false
            },
            coverage: {
                coverageReporter: {
                    reporters: [
                        {type: 'html', dir: path + 'karma/coverage-html'},
                        // TODO: dir should not be needed if we want the output
                        // on screen only - though if we don't specify it is
                        // created. This is probably an issue and we should
                        // report it.
                        {type: 'text', dir: path + 'karma/coverage'}
                    ]
                },
                reporters: [
                    'coverage',
                    'dots'
                ]
            },
            ci: {
                junitReporter: {
                    outputFile: path + 'karma/test-results.xml'
                },
                reporters: [
                    'dots',
                    'junit'
                ]
            },
            'ci-coverage': {
                coverageReporter: {
                    reporters: [
                        {type: 'cobertura', dir: path + 'karma/coverage-xml', file: 'cobertura-coverage.xml'},
                        {type: 'html', dir: path + 'karma/coverage-html'}
                    ]
                },
                junitReporter: {
                    outputFile: path + 'karma/test-results.xml'
                },
                reporters: [
                    'coverage',
                    'dots',
                    'junit'
                ]
            }
        }
    });
};
