variable "hcloud_token" {
  description = "Hetzner API Token"
  sensitive   = true
}

variable "server_name" {
  default = "docker-vm"
}

variable "server_type" {
  default = "cx33"
}

variable "location" {
  default = "hel1"
}

variable "ssh_public_key" {
  description = "Path to SSH public key"
}
