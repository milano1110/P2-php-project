pipeline {
    agent {
        docker {
            image 'composer:latest'
            args '-u root:root'
        }
    }
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
                sh 'vendor/bin/phpunit'
            }
        }
    }
}