# geopagos test backend

## Requirements

* docker
* docker-composer

## Install

```
cp ./backend/.env.example ./backend/.env
docker-compose run --rm backend composer app-setup
```

## Run

```
docker-compose up
```

## Close 

`docker-compose down`

# Dashboard Backend

dashboard
`http://localhost:8000/`

docs
`http://localhost:8000/api/docs`