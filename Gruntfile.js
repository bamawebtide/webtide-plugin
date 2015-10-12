module.exports = function(grunt) {

    grunt.initConfig({
        sass: {
            webtide: {
                options: {
                    sourcemap: 'none',
                    style: 'compressed',
                    noCache: true,
                    update: false
                },
                files: [{
                    expand: true,
                    src: '*.scss',
                    cwd: 'scss',
                    dest: 'css',
                    ext: '.min.css'
                }]
            }
        },
        watch: {
            webtide: {
                files: [ '**/*', '!Gruntfile.js' ],
                tasks: [ 'newer:sass:webtide' ]
            }
        }
    });

    // Load our dependencies
    grunt.loadNpmTasks( 'grunt-contrib-sass' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-newer' );

    // Register our tasks
    grunt.registerTask( 'default', [ 'newer:sass', 'watch' ] );

    // Register a watch function
    grunt.event.on( 'watch', function( action, filepath, target ) {
        grunt.log.writeln( target + ': ' + filepath + ' has ' + action );
    });

};