from php:7.2.2-cli-alpine3.7

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

WORKDIR /widget
COPY ./widget /widget
ENV PYTHONPATH /python_packages

CMD [ "php", "-S", "0.0.0.0:8080", "-t", "public", "public/index.php" ]
