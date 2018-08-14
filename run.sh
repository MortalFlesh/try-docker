#!/bin/bash

echo "clear ..."
docker-compose stop
docker-compose rm -y

echo "build ..."
docker-compose build

clear
echo "run ..."
docker-compose up
