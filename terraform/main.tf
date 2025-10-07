# Odonto360 - Infrastructure as Code (IaC)
# AWS Terraform Configuration

terraform {
  required_version = ">= 1.0"
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 5.0"
    }
  }
}

provider "aws" {
  region = var.aws_region
}

# Data sources
data "aws_availability_zones" "available" {
  state = "available"
}

data "aws_caller_identity" "current" {}

# VPC Configuration
resource "aws_vpc" "odonto360_vpc" {
  cidr_block           = "10.0.0.0/16"
  enable_dns_hostnames = true
  enable_dns_support   = true

  tags = {
    Name        = "odonto360-vpc"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# Internet Gateway
resource "aws_internet_gateway" "odonto360_igw" {
  vpc_id = aws_vpc.odonto360_vpc.id

  tags = {
    Name        = "odonto360-igw"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# Public Subnets
resource "aws_subnet" "public_subnets" {
  count = 2

  vpc_id                  = aws_vpc.odonto360_vpc.id
  cidr_block              = "10.0.${count.index + 1}.0/24"
  availability_zone       = data.aws_availability_zones.available.names[count.index]
  map_public_ip_on_launch = true

  tags = {
    Name        = "odonto360-public-subnet-${count.index + 1}"
    Environment = var.environment
    Project     = "odonto360"
    Type        = "public"
  }
}

# Private Subnets
resource "aws_subnet" "private_subnets" {
  count = 2

  vpc_id            = aws_vpc.odonto360_vpc.id
  cidr_block        = "10.0.${count.index + 10}.0/24"
  availability_zone = data.aws_availability_zones.available.names[count.index]

  tags = {
    Name        = "odonto360-private-subnet-${count.index + 1}"
    Environment = var.environment
    Project     = "odonto360"
    Type        = "private"
  }
}

# Route Table for Public Subnets
resource "aws_route_table" "public_rt" {
  vpc_id = aws_vpc.odonto360_vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.odonto360_igw.id
  }

  tags = {
    Name        = "odonto360-public-rt"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# Route Table Association for Public Subnets
resource "aws_route_table_association" "public_rta" {
  count = length(aws_subnet.public_subnets)

  subnet_id      = aws_subnet.public_subnets[count.index].id
  route_table_id = aws_route_table.public_rt.id
}

# Security Groups
resource "aws_security_group" "web_sg" {
  name_prefix = "odonto360-web-"
  vpc_id      = aws_vpc.odonto360_vpc.id

  ingress {
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "odonto360-web-sg"
    Environment = var.environment
    Project     = "odonto360"
  }
}

resource "aws_security_group" "db_sg" {
  name_prefix = "odonto360-db-"
  vpc_id      = aws_vpc.odonto360_vpc.id

  ingress {
    from_port       = 3306
    to_port         = 3306
    protocol        = "tcp"
    security_groups = [aws_security_group.web_sg.id]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "odonto360-db-sg"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# RDS Subnet Group
resource "aws_db_subnet_group" "odonto360_db_subnet_group" {
  name       = "odonto360-db-subnet-group"
  subnet_ids = aws_subnet.private_subnets[*].id

  tags = {
    Name        = "odonto360-db-subnet-group"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# RDS MySQL Instance
resource "aws_db_instance" "odonto360_mysql" {
  identifier = "odonto360-mysql"

  engine         = "mysql"
  engine_version = "8.0"
  instance_class = var.db_instance_class

  allocated_storage     = 20
  max_allocated_storage = 100
  storage_type          = "gp2"
  storage_encrypted     = true

  db_name  = "odonto360"
  username = var.db_username
  password = var.db_password

  vpc_security_group_ids = [aws_security_group.db_sg.id]
  db_subnet_group_name   = aws_db_subnet_group.odonto360_db_subnet_group.name

  backup_retention_period = 7
  backup_window          = "03:00-04:00"
  maintenance_window     = "sun:04:00-sun:05:00"

  skip_final_snapshot = var.environment != "production"
  deletion_protection = var.environment == "production"

  tags = {
    Name        = "odonto360-mysql"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# Application Load Balancer
resource "aws_lb" "odonto360_alb" {
  name               = "odonto360-alb"
  internal           = false
  load_balancer_type = "application"
  security_groups    = [aws_security_group.web_sg.id]
  subnets            = aws_subnet.public_subnets[*].id

  enable_deletion_protection = var.environment == "production"

  tags = {
    Name        = "odonto360-alb"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# Target Group
resource "aws_lb_target_group" "odonto360_tg" {
  name     = "odonto360-tg"
  port     = 80
  protocol = "HTTP"
  vpc_id   = aws_vpc.odonto360_vpc.id

  health_check {
    enabled             = true
    healthy_threshold   = 2
    interval            = 30
    matcher             = "200"
    path                = "/health"
    port                = "traffic-port"
    protocol            = "HTTP"
    timeout             = 5
    unhealthy_threshold = 2
  }

  tags = {
    Name        = "odonto360-tg"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# ALB Listener
resource "aws_lb_listener" "odonto360_listener" {
  load_balancer_arn = aws_lb.odonto360_alb.arn
  port              = "80"
  protocol          = "HTTP"

  default_action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.odonto360_tg.arn
  }
}

# ECS Cluster
resource "aws_ecs_cluster" "odonto360_cluster" {
  name = "odonto360-cluster"

  setting {
    name  = "containerInsights"
    value = "enabled"
  }

  tags = {
    Name        = "odonto360-cluster"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# ECS Task Definition
resource "aws_ecs_task_definition" "odonto360_task" {
  family                   = "odonto360"
  network_mode             = "awsvpc"
  requires_compatibilities = ["FARGATE"]
  cpu                      = var.ecs_cpu
  memory                   = var.ecs_memory
  execution_role_arn       = aws_iam_role.ecs_execution_role.arn
  task_role_arn            = aws_iam_role.ecs_task_role.arn

  container_definitions = jsonencode([
    {
      name  = "odonto360-app"
      image = "${var.aws_account_id}.dkr.ecr.${var.aws_region}.amazonaws.com/odonto360:latest"
      
      portMappings = [
        {
          containerPort = 80
          hostPort      = 80
          protocol      = "tcp"
        }
      ]

      environment = [
        {
          name  = "APP_ENV"
          value = var.environment
        },
        {
          name  = "DB_HOST"
          value = aws_db_instance.odonto360_mysql.endpoint
        },
        {
          name  = "DB_DATABASE"
          value = aws_db_instance.odonto360_mysql.db_name
        },
        {
          name  = "DB_USERNAME"
          value = aws_db_instance.odonto360_mysql.username
        },
        {
          name  = "DB_PASSWORD"
          value = aws_db_instance.odonto360_mysql.password
        }
      ]

      logConfiguration = {
        logDriver = "awslogs"
        options = {
          "awslogs-group"         = aws_cloudwatch_log_group.odonto360_logs.name
          "awslogs-region"        = var.aws_region
          "awslogs-stream-prefix" = "ecs"
        }
      }
    }
  ])

  tags = {
    Name        = "odonto360-task"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# ECS Service
resource "aws_ecs_service" "odonto360_service" {
  name            = "odonto360-service"
  cluster         = aws_ecs_cluster.odonto360_cluster.id
  task_definition = aws_ecs_task_definition.odonto360_task.arn
  desired_count   = var.ecs_desired_count
  launch_type     = "FARGATE"

  network_configuration {
    subnets          = aws_subnet.private_subnets[*].id
    security_groups  = [aws_security_group.web_sg.id]
    assign_public_ip = false
  }

  load_balancer {
    target_group_arn = aws_lb_target_group.odonto360_tg.arn
    container_name   = "odonto360-app"
    container_port   = 80
  }

  depends_on = [aws_lb_listener.odonto360_listener]

  tags = {
    Name        = "odonto360-service"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# CloudWatch Log Group
resource "aws_cloudwatch_log_group" "odonto360_logs" {
  name              = "/ecs/odonto360"
  retention_in_days = 30

  tags = {
    Name        = "odonto360-logs"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# IAM Roles
resource "aws_iam_role" "ecs_execution_role" {
  name = "odonto360-ecs-execution-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name        = "odonto360-ecs-execution-role"
    Environment = var.environment
    Project     = "odonto360"
  }
}

resource "aws_iam_role_policy_attachment" "ecs_execution_role_policy" {
  role       = aws_iam_role.ecs_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}

resource "aws_iam_role" "ecs_task_role" {
  name = "odonto360-ecs-task-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      }
    ]
  })

  tags = {
    Name        = "odonto360-ecs-task-role"
    Environment = var.environment
    Project     = "odonto360"
  }
}

# Outputs
output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.odonto360_vpc.id
}

output "alb_dns_name" {
  description = "DNS name of the Application Load Balancer"
  value       = aws_lb.odonto360_alb.dns_name
}

output "rds_endpoint" {
  description = "RDS instance endpoint"
  value       = aws_db_instance.odonto360_mysql.endpoint
}

output "ecs_cluster_name" {
  description = "Name of the ECS cluster"
  value       = aws_ecs_cluster.odonto360_cluster.name
}
