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
    }
}