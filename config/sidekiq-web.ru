require 'sidekiq'

redis = { url: "redis://#{ENV.fetch("REDIS_URL")}" }

Sidekiq.configure_client do |config|
  config.redis = redis
end

require 'sidekiq/web'
require 'sidekiq-scheduler/web'

map "/sidekiq-webui" do
  run Sidekiq::Web
end