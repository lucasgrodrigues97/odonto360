# Odonto360 - Terraform Outputs

output "application_url" {
  description = "URL of the deployed application"
  value       = "http://${aws_lb.odonto360_alb.dns_name}"
}

output "database_endpoint" {
  description = "RDS MySQL endpoint"
  value       = aws_db_instance.odonto360_mysql.endpoint
  sensitive   = true
}

output "database_port" {
  description = "RDS MySQL port"
  value       = aws_db_instance.odonto360_mysql.port
}

output "vpc_id" {
  description = "ID of the VPC"
  value       = aws_vpc.odonto360_vpc.id
}

output "public_subnet_ids" {
  description = "IDs of the public subnets"
  value       = aws_subnet.public_subnets[*].id
}

output "private_subnet_ids" {
  description = "IDs of the private subnets"
  value       = aws_subnet.private_subnets[*].id
}

output "security_group_web_id" {
  description = "ID of the web security group"
  value       = aws_security_group.web_sg.id
}

output "security_group_db_id" {
  description = "ID of the database security group"
  value       = aws_security_group.db_sg.id
}

output "ecs_cluster_name" {
  description = "Name of the ECS cluster"
  value       = aws_ecs_cluster.odonto360_cluster.name
}

output "ecs_service_name" {
  description = "Name of the ECS service"
  value       = aws_ecs_service.odonto360_service.name
}

output "load_balancer_dns" {
  description = "DNS name of the load balancer"
  value       = aws_lb.odonto360_alb.dns_name
}

output "cloudwatch_log_group" {
  description = "CloudWatch log group name"
  value       = aws_cloudwatch_log_group.odonto360_logs.name
}
