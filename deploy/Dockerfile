## NOTE: THIS DOCKERFILE IS/was EDITED BY KLEMEN
##
##   NOTE: THIS DOCKERFILE IS GENERATED VIA "update.sh"
##
##   PLEASE DO NOT EDIT IT DIRECTLY.
##

FROM ruby:2.4-jessie

COPY docker-entrypoint /usr/local/bin/
COPY docker-entrypoint /usr/local/bin/

RUN apt-get update && apt-get install -y \
        bash \
        vim \
        nano \
        git \
    --no-install-recommends && rm -r /var/lib/apt/lists/*

ARG ssh_prv_key
ARG ssh_pub_key
ARG github_arg

ARG arg_user_id
RUN adduser deploy_user --quiet --disabled-password --shell /bin/bash --uid $arg_user_id --gecos ""
RUN usermod -u $arg_user_id deploy_user
RUN usermod -a -G root deploy_user

# Authorize SSH Host
RUN mkdir -p /root/.ssh && \
    chmod 0700 /root/.ssh && \
    ssh-keyscan github.com > /root/.ssh/known_hosts

RUN mkdir -p /home/deploy_user/.ssh && \
    chmod 0700 /home/deploy_user/.ssh && \
    chown deploy_user:deploy_user -R /home/deploy_user/ && \
    ssh-keyscan github.com > /home/deploy_user/.ssh/known_hosts && \
    chown deploy_user:deploy_user /home/deploy_user/.ssh/known_hosts   

# Add the keys and set permissions
RUN echo "$ssh_prv_key" > /root/.ssh/id_rsa && \
    echo "$ssh_pub_key" > /root/.ssh/id_rsa.pub && \
    chmod 600 /root/.ssh/id_rsa && \
    chmod 600 /root/.ssh/id_rsa.pub

RUN echo "$ssh_prv_key" > /home/deploy_user/.ssh/id_rsa && \
    echo "$ssh_pub_key" > /home/deploy_user/.ssh/id_rsa.pub && \
    chmod 600 /home/deploy_user/.ssh/id_rsa && \
    chmod 600 /home/deploy_user/.ssh/id_rsa.pub && \
    chown deploy_user:deploy_user -R /home/deploy_user/ 

# Add the keys and set permissions
RUN echo "$github_arg" > /root/.gitconfig && \
    chmod 664 /root/.gitconfig

RUN echo "$github_arg" > /home/deploy_user/.gitconfig && \
    chmod 664 /home/deploy_user/.gitconfig && \
    chown deploy_user:deploy_user /home/deploy_user/.gitconfig

## default for php
#CMD ["php-fpm"]
## default for ruby
# CMD [ "irb" ]
#CMD [ "/bin/bash" ]

USER deploy_user
ENV HOME /home/deploy_user

# Authorize SSH Host (staging.haas.com)
RUN mkdir -p /home/deploy_user/.ssh && \
    echo "|1|kALw7GcQGAKVZ9fZBpIDRU4BRzk=|DDn8L2VblNTNA5pwmiw6NrmReY8= ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBDH7mGUoiR/afPlIYox/8ZUxsQ8h7OMTEtQAJ5CO3kJBHF3ztXcs8oh779BTM71MGpkL9qnyCOE2zWWfcvQ4rAQ=" >> /home/deploy_user/.ssh/known_hosts && \
    chmod 0700 /home/deploy_user/.ssh && \
    chown deploy_user:deploy_user /home/deploy_user/.ssh

WORKDIR /var/www/html/deploy
ENTRYPOINT ["docker-entrypoint"]
CMD [ "/bin/bash" ]