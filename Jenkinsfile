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
                sh '''
                    echo "Waiting for MySQL to be ready..."
                    while ! docker compose exec db mysqladmin ping -hlocalhost -ularaveluser -psecret --silent; do
                        sleep 1
                    done
                    echo "MySQL is ready!"
                '''
            }
        }

        stage("Run Migrations") {
            steps {
                sh 'docker compose run --rm app php artisan migrate --force'
            }
        }

        stage("Run Tests") {
            steps {
                sh 'docker compose ps'
                sh 'docker compose run --rm app php artisan test'
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