pipeline {
    agent any
    stages {
        stage('Install Dependencies') {
            steps {
                echo 'install';
            }
        }
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
            steps {
                echo 'test';
            }
        }
    }
}
