set :application, "mura-show"
set :domain,      "mura-show.com"
set :deploy_to,   "/var/www/rodger/www.mura-show.com"
set :app_path,    "app"
set :web_path,    "web"

set :repository,  "git@github.com:rodgermd/mura-show.com.git"
set :scm,         :git

set :model_manager, "doctrine"

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This may be the same as your `Web` server

set :keep_releases,   10
set :shared_children, [app_path + "/logs", app_path + "/cache/sessions", app_path + "uploads", web_path + "/media"]
set :shared_files,    ["app/config/parameters.private.yml"]
set :use_composer,    true
set :update_vendors,  false
set :use_sudo,        true
set :user,            "rodger"

set :writable_dirs,     ["app/cache", "app/logs", "web", "web/media"]
set :webserver_user,    "www-data"
set :permission_method, :acl

set :dump_assetic_assets, true
set :interactive_mode, true

logger.level = Logger::MAX_LEVEL

after "deploy", "deploy:set_permissions"