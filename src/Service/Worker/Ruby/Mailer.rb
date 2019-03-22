class Mailer
    include Sidekiq::Worker

    def perform(*arguments)
        symfony_env = ENV.fetch("APP_ENV", "dev")
        cmd = "php bin/console worker:exec -e #{symfony_env} --no-debug"
        args = arguments.to_json

        logger.info "Calling #{cmd} Mailer '#{args}'"

        # call SF2 command and redirect stderr with 2>&1
        output=`#{cmd} Mailer '#{args}' 2>&1`; result=$?.success?

        if result != true
            raise output
        end
    end
end