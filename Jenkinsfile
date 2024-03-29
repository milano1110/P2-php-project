pipeline {
    agent any
    stages {
        stage('SonarQube') {
            steps {
                script {
                def scannerHome = tool 'SonarQube Scanner';
                    withSonarQubeEnv('SonarQube') {
                        sh "${scannerHome}/bin/sonar-scanner"
                    }
                }
            }
        }
        // stage('Build') {
        //     steps {
        //         sh 'docker-compose up --build'
        //     }
        // }
        stage('Test') {
            steps {
                unstash 'vendor'
                sh 'vendor/bin/phpunit'
                xunit(
                    thresholds: [
                        failed(failureThreshold: '0'),
                        skipped(unstableThreshold: '0')
                    ],
                    tools: [
                        PHPUnit(
                            pattern: 'build/logs/junit.xml',
                            stopProcessingIfError: true,
                            failIfNotNew: true
                        )
                    ]
                )
            }
        }
    }
}