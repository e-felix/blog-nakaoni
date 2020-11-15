# -*- mode: ruby -*-
# vi: set ft=ruby ts=2 sw=2 expandtab :

PROJECT = "blog-nakaoni"

DATABASE_NAME = "vagrant"
DATABASE_HOST = "blog-nakaoni_db"
DATABASE_USER = "vagrant"
DATABASE_PASSWORD = "vagrant"

ENV['VAGRANT_NO_PARALLEL'] = 'yes'
ENV['VAGRANT_DEFAULT_PROVIDER'] = 'docker'
Vagrant.configure(2) do |config|

  config.ssh.insert_key = false

  config.vm.define "db" do |db|
    db.vm.provider "docker" do |d|
      d.image = "mysql:5.6"
      d.name = "#{PROJECT}_db"
      d.env = {
        "MYSQL_ROOT_PASSWORD" => DATABASE_PASSWORD,
        "MYSQL_PASSWORD" => DATABASE_PASSWORD,
        "MYSQL_USER" => DATABASE_USER,
        "MYSQL_DATABASE" => DATABASE_NAME,
      }
    end
  end

  config.vm.define "dev", primary: true do |app|
    app.vm.provider "docker" do |d|

      d.image = "jean553/php-dev"
      d.name = "#{PROJECT}_dev"
      d.link "#{PROJECT}_db:db"
      d.has_ssh = true
      d.env = {
        "HOST_USER_UID" => Process.euid,

        "DATABASE_NAME" => "#{DATABASE_NAME}",
        "DATABASE_HOST" => "#{DATABASE_HOST}",
        "DATABASE_USER" => "#{DATABASE_USER}",
        "DATABASE_PASSWORD" => "#{DATABASE_PASSWORD}",

        # FIXME: #7 many warning messages are displayed when running tests,
        # mainly because of deprecations from public bundles,
        # this line simply hides them without fixing the issue
        "SYMFONY_DEPRECATIONS_HELPER" => "weak",

        "MAILER_SOURCE_EMAIL" => "noreply@projet.com",
        "BASE_URL" => "http://localhost:8080"
      }
    end
    app.vm.network "forwarded_port", guest: 8000, host: 8080
    app.ssh.username = "vagrant"
  end

  config.vm.provision "installs", "type": "shell" do |installs|
    installs.inline = "

      apt-get update
      apt-get install php-mbstring php-zip zip mysql-server php7.2-mysql htop php-xdebug wget -y

      cd /vagrant/blog-nakaoni
      curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
      composer install

      # prevents SessionHandler::gc(): ps_files_cleanup_dir error
      chown vagrant:vagrant /var/lib/php/sessions -R

      echo 'cd /vagrant/blog-nakaoni' >> /home/vagrant/.zshrc

      wget https://get.symfony.com/cli/installer -O - | bash
      mv /home/vagrant/.symfony/bin/symfony /usr/local/bin/symfony

      echo 'Done.'
    "
  end
end
