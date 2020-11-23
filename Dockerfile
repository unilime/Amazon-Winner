FROM php:7.4-cli

RUN mkdir /var/opt/marakasdesign
WORKDIR /var/opt/marakasdesign

# install packages
RUN apt-get update && \
    apt-get install --no-install-recommends --assume-yes --quiet ca-certificates curl git vim zip unzip && \
    rm -rf /var/lib/apt/lists/*

# install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"  && \
    php composer-setup.php --filename=composer --install-dir=/usr/local/bin  && \
    php -r "unlink('composer-setup.php');"