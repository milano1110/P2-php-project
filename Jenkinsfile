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
        stage('Test') {
            agent {
                docker {
                    image 'php:8.3-cli'
                }
            }
            steps {
                sh 'app/vendor/bin/phpunit'
            }
        }
    }
}