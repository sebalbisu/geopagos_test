#!/bin/bash

# remove data
sudo rm -rf ./data/mysql/data/*
sudo rm -rf ./data/redis/*
sudo rm -rf ./data/postgres/*

# remove backend storage
directories=(
  "./data/backend/storage/app/public"
  "./data/backend/storage/app/photo"
  "./data/backend/storage/framework"
  "./data/backend/storage/logs"
)
for dir in "${directories[@]}"; do
  sudo find "$dir"/* -type f -not -name '.gitignore' -delete
  sudo find "$dir"/*/* -type d -empty -delete
  sudo find "$dir"/* -type d -empty
done

sudo rm -rf ./backend/vendor

