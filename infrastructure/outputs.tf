# Outputs for Odonto360 AWS infrastructure

output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.main.id
}

output "vpc_cidr_block" {
  description = "CIDR block of the VPC"
  value       = aws_vpc.main.cidr_block
}

output "public_subnet_ids" {
  description = "IDs of the public subnets"
  value       = aws_subnet.public[*].id
}

output "private_subnet_ids" {
  description = "IDs of the private subnets"
  value       = aws_subnet.private[*].id
}

output "internet_gateway_id" {
  description = "ID of the Internet Gateway"
  value       = aws_internet_gateway.main.id
}

output "load_balancer_dns" {
  description = "DNS name of the Application Load Balancer"
  value       = aws_lb.main.dns_name
}

output "load_balancer_zone_id" {
  description = "Zone ID of the Application Load Balancer"
  value       = aws_lb.main.zone_id
}

output "load_balancer_arn" {
  description = "ARN of the Application Load Balancer"
  value       = aws_lb.main.arn
}

output "target_group_arn" {
  description = "ARN of the target group"
  value       = aws_lb_target_group.main.arn
}

output "database_endpoint" {
  description = "RDS instance endpoint"
  value       = aws_db_instance.main.endpoint
  sensitive   = true
}

output "database_port" {
  description = "RDS instance port"
  value       = aws_db_instance.main.port
}

output "database_name" {
  description = "RDS database name"
  value       = aws_db_instance.main.db_name
}

output "database_username" {
  description = "RDS database username"
  value       = aws_db_instance.main.username
  sensitive   = true
}

output "s3_bucket_name" {
  description = "Name of the S3 bucket for file storage"
  value       = aws_s3_bucket.main.bucket
}

output "s3_bucket_arn" {
  description = "ARN of the S3 bucket"
  value       = aws_s3_bucket.main.arn
}

output "cloudwatch_log_group_name" {
  description = "Name of the CloudWatch log group"
  value       = aws_cloudwatch_log_group.main.name
}

output "cloudwatch_log_group_arn" {
  description = "ARN of the CloudWatch log group"
  value       = aws_cloudwatch_log_group.main.arn
}

output "security_group_web_id" {
  description = "ID of the web security group"
  value       = aws_security_group.web.id
}

output "security_group_database_id" {
  description = "ID of the database security group"
  value       = aws_security_group.database.id
}

output "launch_template_id" {
  description = "ID of the launch template"
  value       = aws_launch_template.main.id
}

output "launch_template_latest_version" {
  description = "Latest version of the launch template"
  value       = aws_launch_template.main.latest_version
}

output "autoscaling_group_name" {
  description = "Name of the Auto Scaling Group"
  value       = aws_autoscaling_group.main.name
}

output "autoscaling_group_arn" {
  description = "ARN of the Auto Scaling Group"
  value       = aws_autoscaling_group.main.arn
}

output "application_url" {
  description = "URL of the application"
  value       = "http://${aws_lb.main.dns_name}"
}

output "health_check_url" {
  description = "URL of the health check endpoint"
  value       = "http://${aws_lb.main.dns_name}/api/health"
}

output "infrastructure_summary" {
  description = "Summary of the deployed infrastructure"
  value = {
    vpc_id                = aws_vpc.main.id
    vpc_cidr             = aws_vpc.main.cidr_block
    public_subnets       = aws_subnet.public[*].id
    private_subnets      = aws_subnet.private[*].id
    load_balancer_dns    = aws_lb.main.dns_name
    database_endpoint    = aws_db_instance.main.endpoint
    s3_bucket           = aws_s3_bucket.main.bucket
    application_url     = "http://${aws_lb.main.dns_name}"
    health_check_url    = "http://${aws_lb.main.dns_name}/api/health"
  }
}

output "deployment_instructions" {
  description = "Instructions for deploying the application"
  value = <<-EOT
    To deploy the Odonto360 application:
    
    1. Connect to one of the EC2 instances:
       ssh -i your-key.pem ec2-user@<instance-ip>
    
    2. Navigate to the application directory:
       cd /var/www/html/odonto360
    
    3. Run the deployment script:
       sudo ./deploy.sh
    
    4. Check the application status:
       sudo ./status.sh
    
    5. Access the application:
       http://${aws_lb.main.dns_name}
    
    6. Health check:
       http://${aws_lb.main.dns_name}/api/health
    
    Database connection details:
    - Host: ${aws_db_instance.main.endpoint}
    - Port: ${aws_db_instance.main.port}
    - Database: ${aws_db_instance.main.db_name}
    - Username: ${aws_db_instance.main.username}
    - Password: [stored in .env file]
    
    S3 Bucket for file storage:
    - Bucket: ${aws_s3_bucket.main.bucket}
    - Region: ${var.aws_region}
  EOT
}
