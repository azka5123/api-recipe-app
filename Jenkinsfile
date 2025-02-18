pipeline {
    agent any
    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    whoami
                    newgrp docker
                    docker info
                    docker version
                    docker compose version
                '''
            }
        }
        stage("Clear all running docker containers") {
            steps {
                script {
                    try {
                        sh 'docker rm -f $(docker ps -a -q)'
                    } catch (Exception e) {
                        echo 'No running container to clear up...'
                    }
                }
            }
        }
        stage("Start Docker") {
            steps {
                sh 'make up'
                sh 'docker compose ps'
            }
        }
        stage("Run Composer Install") {
            steps {
                sh 'docker compose run --rm composer install'
            }
        }
        stage("Populate .env file") {
            steps {
                dir("/var/lib/jenkins/workspace/envs/app_recipe") {
                    fileOperations([fileCopyOperation(excludes: '', flattenFiles: true, includes: '.env', targetLocation: "${WORKSPACE}")])
                }
            }
        }              
        stage("Run Tests") {
            steps {
                sh 'docker compose run --rm artisan test'
            }
        }
    }
    post {
        success {
            sh 'cd "/var/lib/jenkins/workspace/app_recipe"'
            sh 'rm -rf artifact.zip'
            sh 'zip -r artifact.zip . -x "*node_modules**"'                              
        }
        always {
            sh 'docker compose down --remove-orphans -v'
            sh 'docker compose ps'
        }
    }
}