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
                sh 'curl -sS https://getcomposer.org/installer | php'
                withEnv(["PATH+COMPOSER=${WORKSPACE}"]) {
                    sh 'composer install'
                    sh 'app/vendor/bin/phpunit'
                }
            }
        }
    }
}