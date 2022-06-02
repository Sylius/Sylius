FROM python:3-alpine

WORKDIR /usr/src/app

RUN apk add --no-cache git gcc libffi-dev musl-dev

COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

CMD ["/bin/sh", "build.sh"]
