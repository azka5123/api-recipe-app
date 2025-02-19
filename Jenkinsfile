pipeline {
    agent any

    stages {
        stage("Verify tooling") {
            steps {
                sh '''
                    git --version
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

        stage('Checkout') {
            steps {
                script {
                    sh 'git config --global --add safe.directory "*"'
                    checkout scm
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
                sh 'docker compose run --rm app composer install'
            }
        }

        stage("Populate .env file") {
            steps {
                sh 'docker compose exec app chmod -R 777 storage bootstrap/cache'
                script {
                    def envFile = '/var/lib/jenkins/workspace/envs/app_recipe/.env'
                    if (fileExists(envFile)) {
                        sh "cp ${envFile} ${WORKSPACE}/.env"
                    } else {
                        echo "Warning: .env file not found at ${envFile}"
                    }
                }
            }
        }

        stage("Wait for Database") {
            steps {
                sh 'docker compose ps'
                sh '''
                    echo "Waiting for MySQL to be ready..."
                    while ! docker compose exec db mysqladmin ping -hlocalhost -ularaveluser -psecret --silent; do
                        sleep 1
                    done
                    echo "MySQL is ready!"
                '''
            }
        }
    }

    post {
        success {
            sh 'docker-compose exec app php artisan migrate --force'
            sh 'docker compose ps'
        }
        // always {
        //     sh 'docker compose down --remove-orphans -v'
        //     sh 'docker compose ps'
        // }
    }
}