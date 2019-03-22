require 'sidekiq'
require 'sidekiq-scheduler'

redis = { url: "redis://#{ENV.fetch("REDIS_URL")}" }
# If your client is single-threaded, we just need a single connection in our Redis connection pool
Sidekiq.configure_client do |config|
  config.redis = redis
end

# Sidekiq server is multi-threaded so our Redis connection pool size defaults to concurrency (-c)
Sidekiq.configure_server do |config|
  config.redis = redis
end

Dir[File.dirname(__FILE__) + '/../src/Service/Worker/Ruby/*.rb'].each {|file| require file }
