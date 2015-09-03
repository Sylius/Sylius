# Override the path before running the setup
set :deploy_config_path, 'deploy/deploy.rb'
set :stage_config_path, 'deploy/stages/'

# Load DSL and set up stages
require 'capistrano/setup'

# Include default deployment tasks
require 'capistrano/deploy'
require 'capistrano/symfony'

# Override the default path to bundle deployments scripts and tasks
Dir.glob('deploy/tasks/*.cap').each { |r| import r }