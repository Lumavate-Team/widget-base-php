from php:7.3-cli-alpine3.9 as common

COPY requirements.txt .

RUN apk add --no-cache --virtual .build-deps \
    gcc \
    git \
    libc-dev \
    libgcc \
    linux-headers \
    python \
    python3-dev \
  && apk add --no-cache \
    curl \
    python3 \
  && pip3 install -r requirements.txt \
  && mkdir /python_packages \
  && cd /python_packages \
  && git clone https://github.com/Lumavate-Team/python-signer.git lumavate_signer \
  && rm -rf /python_packages/lumavate_signer/.git \
  && apk del .build-deps \
  && mkdir -p /widget

FROM quay.io/lumavate/edit:base

RUN apk --no-cache add \
        php7 \
        php7-ctype \
        php7-curl \
        php7-dom \
        php7-fileinfo \
        php7-ftp \
        php7-iconv \
        php7-json \
        php7-mbstring \
        php7-mysqlnd \
        php7-openssl \
        php7-pdo \
        php7-pdo_sqlite \
        php7-pear \
        php7-phar \
        php7-posix \
        php7-session \
        php7-simplexml \
        php7-sqlite3 \
        php7-tokenizer \
        php7-xml \
        php7-xmlreader \
        php7-xmlwriter \
        php7-zlib
RUN set -x \
    && addgroup -g 82 -S www-data \
    && adduser -u 82 -D -S -G www-data www-data

EXPOSE 8080

COPY supervisord.conf /etc/supervisor/conf.d

COPY --from=common /python_packages ./python_packages/

WORKDIR /widget
COPY ./widget /widget

ENV PYTHONPATH /python_packages
ENV PROJECT_ROOT /widget

CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
