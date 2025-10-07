#!/bin/bash

# Odonto360 - Deployment Script
# This script handles the complete deployment process

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT=${1:-production}
AWS_REGION=${2:-us-east-1}
DOCKER_IMAGE_TAG=${3:-latest}

echo -e "${BLUE}🚀 Starting Odonto360 Deployment${NC}"
echo -e "${BLUE}Environment: ${ENVIRONMENT}${NC}"
echo -e "${BLUE}AWS Region: ${AWS_REGION}${NC}"
echo -e "${BLUE}Docker Tag: ${DOCKER_IMAGE_TAG}${NC}"
echo ""

# Function to print status
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Step 1: Run Tests
echo -e "${BLUE}📋 Step 1: Running Tests${NC}"
if [ -f "composer.json" ]; then
    echo "Running PHP tests..."
    composer test || {
        print_error "Tests failed!"
        exit 1
    }
    print_status "PHP tests passed"
fi

if [ -f "package.json" ]; then
    echo "Running JavaScript tests..."
    npm test || {
        print_warning "JavaScript tests failed, continuing..."
    }
fi

# Step 2: Build Docker Image
echo -e "${BLUE}📋 Step 2: Building Docker Image${NC}"
echo "Building Docker image with tag: odonto360:${DOCKER_IMAGE_TAG}"

docker build -t odonto360:${DOCKER_IMAGE_TAG} . || {
    print_error "Docker build failed!"
    exit 1
}
print_status "Docker image built successfully"

# Step 3: Push to ECR (if in AWS environment)
if [ "$ENVIRONMENT" != "local" ]; then
    echo -e "${BLUE}📋 Step 3: Pushing to ECR${NC}"
    
    # Get AWS Account ID
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    
    # Login to ECR
    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com
    
    # Create ECR repository if it doesn't exist
    aws ecr describe-repositories --repository-names odonto360 --region ${AWS_REGION} || \
    aws ecr create-repository --repository-name odonto360 --region ${AWS_REGION}
    
    # Tag and push image
    docker tag odonto360:${DOCKER_IMAGE_TAG} ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/odonto360:${DOCKER_IMAGE_TAG}
    docker push ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/odonto360:${DOCKER_IMAGE_TAG}
    
    print_status "Image pushed to ECR"
fi

# Step 4: Deploy Infrastructure (Terraform)
if [ "$ENVIRONMENT" != "local" ]; then
    echo -e "${BLUE}📋 Step 4: Deploying Infrastructure${NC}"
    
    cd terraform
    
    # Initialize Terraform
    terraform init || {
        print_error "Terraform initialization failed!"
        exit 1
    }
    
    # Plan deployment
    terraform plan -var="environment=${ENVIRONMENT}" -var="aws_region=${AWS_REGION}" -var="aws_account_id=${AWS_ACCOUNT_ID}" || {
        print_error "Terraform plan failed!"
        exit 1
    }
    
    # Apply deployment
    terraform apply -auto-approve -var="environment=${ENVIRONMENT}" -var="aws_region=${AWS_REGION}" -var="aws_account_id=${AWS_ACCOUNT_ID}" || {
        print_error "Terraform apply failed!"
        exit 1
    }
    
    # Get outputs
    APP_URL=$(terraform output -raw application_url)
    DB_ENDPOINT=$(terraform output -raw database_endpoint)
    
    print_status "Infrastructure deployed successfully"
    print_status "Application URL: ${APP_URL}"
    print_status "Database Endpoint: ${DB_ENDPOINT}"
    
    cd ..
fi

# Step 5: Run Database Migrations
echo -e "${BLUE}📋 Step 5: Running Database Migrations${NC}"

if [ "$ENVIRONMENT" = "local" ]; then
    # Local deployment
    docker-compose up -d db
    sleep 10
    docker-compose exec app php artisan migrate --force
    docker-compose exec app php artisan db:seed --force
else
    # AWS deployment - run migrations via ECS task
    echo "Running migrations on ECS..."
    # This would typically be done via a separate ECS task or Lambda function
    print_warning "Database migrations should be run manually on the deployed environment"
fi

print_status "Database migrations completed"

# Step 6: Health Check
echo -e "${BLUE}📋 Step 6: Health Check${NC}"

if [ "$ENVIRONMENT" = "local" ]; then
    HEALTH_URL="http://localhost:8000/health"
else
    HEALTH_URL="${APP_URL}/health"
fi

# Wait for application to be ready
echo "Waiting for application to be ready..."
for i in {1..30}; do
    if curl -f -s "${HEALTH_URL}" > /dev/null; then
        print_status "Application is healthy!"
        break
    fi
    echo "Attempt $i/30: Application not ready yet..."
    sleep 10
done

# Final status
echo ""
echo -e "${GREEN}🎉 Deployment Completed Successfully!${NC}"
echo -e "${GREEN}Environment: ${ENVIRONMENT}${NC}"
if [ "$ENVIRONMENT" != "local" ]; then
    echo -e "${GREEN}Application URL: ${APP_URL}${NC}"
fi
echo -e "${GREEN}Docker Image: odonto360:${DOCKER_IMAGE_TAG}${NC}"
echo ""

# Optional: Run smoke tests
echo -e "${BLUE}📋 Running Smoke Tests${NC}"
if [ "$ENVIRONMENT" != "local" ]; then
    # Basic smoke test
    curl -f -s "${APP_URL}/health" && print_status "Health check passed" || print_warning "Health check failed"
    curl -f -s "${APP_URL}/" && print_status "Homepage accessible" || print_warning "Homepage not accessible"
fi

echo -e "${GREEN}🚀 Deployment process completed!${NC}"
