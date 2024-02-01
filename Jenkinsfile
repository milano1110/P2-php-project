pipeline {
    agent any
    stage('SonarQube analysis') {
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