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
                sh 'composer install'
                sh 'app/vendor/bin/phpunit'
            }
        }
    }
}