# geopagos test backend

## Requirements

* docker
* docker-composer

## Install

```
cp ./backend/.env.example ./backend/.env
docker-compose run --rm backend composer app-setup
```

## Service Up

```
docker-compose up
```

## Service Down

```
docker-compose down
```

## Tests

from outside
```
docker-compose run backend bash -c "composer app-test"
```

from inside docker
```
composer app-test
```

## Docs

```
http://localhost:8000/api/docs
```
