resource "hcloud_ssh_key" "default" {
  name       = "hetzner-forus-key"
  public_key = file(var.ssh_public_key)
}

resource "hcloud_firewall" "docker_fw" {
  name = "docker-fw"

  rule {
    direction  = "in"
    protocol   = "tcp"
    port       = "22"
    source_ips = ["0.0.0.0/0"]
  }

  rule {
    direction  = "in"
    protocol   = "tcp"
    port       = "80"
    source_ips = ["0.0.0.0/0"]
  }

  rule {
    direction  = "in"
    protocol   = "tcp"
    port       = "443"
    source_ips = ["0.0.0.0/0"]
  }
}

resource "hcloud_server" "forus_digitalvm" {
  name        = var.server_name
  image       = "ubuntu-24.04"
  server_type = var.server_type
  location    = var.location

  ssh_keys = [hcloud_ssh_key.default.id]

  user_data = file("cloud-init.yml")

  firewall_ids = [hcloud_firewall.docker_fw.id]
}
