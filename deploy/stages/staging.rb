require 'net/ssh/proxy/command'

set :application, "haas-api"
server "35.176.72.201", user: "ubuntu", roles: %w{app db web sidekiq}

set :bundle_gemfile,  "deploy/Gemfile"
set :symfony_env,  "prod"
set :APP_ENV,  "prod"
set :branch, "develop"
set :deploy_to, "/var/www/haas.nv3.eu/webdir"
set :app_path, nil
set :app_config_path, nil
set :cache_path, "var/cache"

set :web_path, "public"
set :assets_install_path, fetch(:web_path)

set :file_permissions_roles, "app"
set :permission_method, :acl
set :file_permissions_users, ["www-data"]
set :file_permissions_paths, ["var/log", "var/cache"]

set :composer_install_flags, '--no-dev --no-interaction --optimize-autoloader'

set :log_path,  fetch(:var_path) + "/log"
set :linked_dirs, [fetch(:log_path), 'var/pids']
set :linked_files, ["var/jwt/private.pem", "var/jwt/public.pem"]
set :symfony_deploy_roles, 'all'

namespace :deploy do
  task :set_symfony_env do
    fetch(:default_env).merge!(APP_ENV: fetch(:APP_ENV) || 'prod')
  end
end

set :rvm_ruby_version, 'ruby-2.4.3@haas --create'
set :rvm_type, :user
set :log_level, :debug
# set :rvm_roles, :sidekiq
# set :bundle_roles, :sidekiq
set :symfony_roles, :all

after 'deploy:updated', 'deploy:migrate'
# after 'deploy:finished', 'deploy:sidekiq_restart'
namespace :deploy do
  desc 'Perform migrations : doctrin_migrations_migrate'
  task :migrate do
    on roles(:db) do
      symfony_console('doctrine:migrations:migrate', '--no-interaction')
    end
  end
#  desc 'Restart sidekiq : monit restart sidekiq'
#  task :sidekiq_restart do
#    on roles(:sidekiq) do
#      execute 'sudo', :monit, :restart, 'sidekiq'
#    end
#  end
end