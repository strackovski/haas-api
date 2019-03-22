class Example
    include Sidekiq::Worker

    def perform(*arguments)
        cmd = "php bin/console worker:exec -e dev --no-debug"
        args = arguments.to_json

        logger.info "Calling #{cmd} Example '#{args}'"

        # call SF2 command and redirect stderr with 2>&1
        output=`#{cmd} Example '#{args}' 2>&1`; result=$?.success?

        if result != true
            raise output
        end
    end
end