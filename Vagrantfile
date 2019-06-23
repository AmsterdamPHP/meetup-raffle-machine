# vagrant init ubuntu/xenial64

Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/xenial64"

    config.vm.network :private_network, ip: "10.10.10.10"

    config.vm.provider :virtualbox do |v|
        v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        v.customize ["modifyvm", :id, "--memory", 1024]
        v.customize ["modifyvm", :id, "--name", "AmsterdamPHP Raffler"]
    end

    config.vm.provision "shell" do |s|
        s.inline = "sudo apt-get update && sudo apt-get install -y python"
    end

    config.vm.provision "ansible" do |ansible|
        ansible.playbook = "ansible/provision.yml"
        ansible.extra_vars = {
            hostname: "raffler",
            dbuser: "root",
            dbpasswd: "password",
            databases: ["development"],
            sites: [
                {
                    hostname: "raffler.local",
                    document_root: "/vagrant/web"
                }
            ],
            php_configs: [
            ],
            install_postgresql: "no",
            postgresql_version: "9.5",
            postgresql_user: "root",
            postgresql_passwd: "password",
            postgresql_databases: ["development"],
            install_gems: [],
            install_db: "no",
            install_ohmyzsh: "yes",
            install_web: "yes",
            install_mailcatcher: "no",
            install_hhvm: "no",
            install_beanstalkd: "no",
            install_redis: "yes",
            install_javascript_build_system: "yes",
            install_r: "no",
            install_rabbit_mq: "no",
            r_packages: [],
            enable_swap: "yes",
            swap_size_in_mb: "1024",
            install_eventstore: "no",
            eventstore_version: "3.0.1",
            eventstore_bind_ip: "10.10.10.10",
            eventstore_http_prefix: "http://app.local:2113/"
        }
    end

    config.vm.provision "shell" do |s|
        s.inline = "cd /vagrant; make install"
    end
end
