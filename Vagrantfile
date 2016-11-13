# vagrant init ubuntu/trusty64

Vagrant.configure("2") do |config|
    config.vm.box = "trusty64"
    config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/trusty/current/trusty-server-cloudimg-amd64-vagrant-disk1.box"

    config.vm.network :private_network, ip: "10.10.10.10"
    config.vm.hostname = "app.local"

    config.vm.provider :virtualbox do |v|
        v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
        v.customize ["modifyvm", :id, "--memory", 1024]
        v.customize ["modifyvm", :id, "--name", "Meetup Raffle Machine"]
    end

    config.vm.provider :libvirt do |domain|
        domain.memory = 1024
    end

    if (/linux/ =~ RUBY_PLATFORM) != nil
        config.vm.synced_folder "./", "/vagrant", id: "vagrant-root", nfs: true, :linux__nfs_options => ['rw','no_subtree_check','all_squash','async']
    else
        config.vm.synced_folder "./", "/vagrant", id: "vagrant-root", :nfs => true
    end

    if not Vagrant.has_plugin?("vagrant-hostmanager")
        if system "vagrant plugin install vagrant-hostmanager"
          exec "vagrant #{ARGV.join(' ')}"
        else
          abort "Aborting due to plugin installation failure."
        end
    end

    config.hostmanager.enabled = true
    config.hostmanager.manage_host = true
    config.hostmanager.include_offline = true

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
            install_javascript_build_system: "yes"
        }
    end

    config.vm.provision "shell", path: "post-install.sh"
end
