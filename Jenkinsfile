#!groovy
node {
    stage 'Checkout'
        checkout scm

    dir('symfony') {
        stage 'build'
            parallel 'build php': {
                sh 'make composer-install'            
            }, 'build js': {
                sh 'make npm-install'
                sh 'make gulp-default'
            }

        stage 'tests + tarball'
            parallel 'lint': {
                sh 'make lint'
            }, 'phpunit': {
                sh 'make phpunit-report'
            }, 'phpcs': {
                sh returnStatus: true, script: 'make phpcs-report'
            }, 'phpmd': {
                sh returnStatus: true, script: 'make phpmd-report'
            }, 'phpcpd': {
                sh 'make phpcpd-report'
            }, 'tarball': {
                sh 'make tarball'
            }

        stage 'Publish Results'
            junit 'reports/*.junit.xml'

            publishHTML(target: [
                allowMissing: false, 
                alwaysLinkToLastBuild: false,
                keepAll: false,
                reportDir: 'reports/phpunit-html-coverage',
                reportFiles: 'index.html',
                reportName: 'PHPUnit Code Coverage Report'
            ])

            step([
                $class: 'CloverPublisher', 
                cloverReportDir: 'reports', 
                cloverReportFileName: '*.clover.xml'
            ])

            step([
                $class: 'CheckStylePublisher',
                canComputeNew: false,
                defaultEncoding: '',
                healthy: '',
                pattern: 'reports/*.cs.xml',
                unHealthy: ''
            ])

            step([
                $class: 'DryPublisher',
                canComputeNew: false,
                defaultEncoding: '',
                healthy: '',
                normalThreshold: 10,
                pattern: 'reports/*.dry.xml',
                unHealthy: ''
            ])

            step([
                $class: 'PmdPublisher',
                canComputeNew: false,
                defaultEncoding: '',
                healthy: '',
                pattern: 'reports/*.pmd.xml',
                unHealthy: ''
            ])
    }
    
    dir('ansible') {
        stage 'deploy-stage'
            sh 'make BRANCH=$BRANCH_NAME deploy-branch'
        stage 'profile-stage'
            // could be used somewhere else
            sh 'blackfire curl http://$BRANCH_NAME.chess-db-stage.foo'
    }
}

if ('master' == env.BRANCH_NAME) {
    timeout(time:30, unit:'MINUTES') {
        input message:'Deploy to live?'
    }

    node {
        dir('ansible') {
            stage 'deploy-live'
                sh 'make deploy'
        }
    }
}