Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  config.ssh.forward_agent = true

  # VirtualBox
  config.vm.provider :virtualbox do |v, override|
    override.vm.network "private_network", ip: "10.0.0.200"
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--memory", 1024]
    v.customize ["modifyvm", :id, "--name", "sylius"]
  end

  # VMWare Workstation
  config.vm.provider "vmware_workstation" do |v, override|
    override.vm.box = "puphpet/ubuntu1404-x64"
    override.vm.network "private_network", ip: "113.0.0.11"
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--memory", 1024]
    v.customize ["modifyvm", :id, "--name", "sylius"]
  end

  nfs_setting = RUBY_PLATFORM =~ /darwin/ || RUBY_PLATFORM =~ /linux/
  config.vm.synced_folder "./../../", "/var/www/sylius", id: "vagrant-root" , :nfs => nfs_setting
  config.vm.provision :shell do |shell|
    shell.path = "bootstrap.sh"
  end

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "manifests"
    puppet.options = ['--verbose']
  end
end
