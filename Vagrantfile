# vagrant init ubuntu/trusty64

Vagrant.configure("2") do |config|
    config.vm.box = "trusty64"
    config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"

    config.vm.network :private_network, ip: "10.10.10.10"
    config.vm.hostname = "app.local"

    config.vm.provider :virtualbox do |v|
        v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        v.customize ["modifyvm", :id, "--memory", 512]
        v.customize ["modifyvm", :id, "--name", "Meetup Raffle Machine"]
    end

    if Vagrant.has_plugin?("vagrant-hostmanager")
        config.hostmanager.enabled = true
        config.hostmanager.manage_host = true
        config.hostmanager.include_offline = true
    end

    config.vm.provision "ansible" do |ansible|
        ansible.playbook = "ansible/provision.yml"
        ansible.extra_vars = {
            hostname: "dev",
            dbuser: "root",
            dbpasswd: "password",
            databases: ["development"],
            sites: [
                {
                    hostname: "app.local",
                    document_root: "/vagrant/web"
                }
            ],
            install_db: "no",
            install_ohmyzsh: "yes",
            install_web: "yes",
            install_mailcatcher: "no",
            install_hhvm: "no",
            install_beanstalkd: "no",
            install_redis: "yes",
            install_javascript_build_system: "no"
        }
    end
end

Vagrant::Config.run do |config|
    config.vm.provision :shell do |shell|
        shell.inline = "sudo gem install compass --no-ri --no-rdoc && sudo gem install susy --no-ri --no-rdoc"
    end
end
