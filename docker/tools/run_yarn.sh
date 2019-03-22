#!/bin/bash
## Create for nginx
if [[ ! -d "./assets" || ! -f "./webpack.config.js" || ! -f "./package.json" ]]; then
  echo "You should run this script from the root of your project (from the root of github repo)."
  echo "You shall not pass."
  exit 1
fi

echo "Running yarn thingies"
if [ $# -eq 0 ]
  then
	echo "Running yarn add @symfony/webpack-encore --dev"
	docker run --rm --name node_yarn -v $(pwd):/code -w /code node:6 yarn add @symfony/webpack-encore --dev
	echo "Running yarn install"
	docker run --rm --name node_yarn -v $(pwd):/code -w /code node:6 yarn install
	echo "Running yarn run encore production"
	docker run --rm --name node_yarn -v $(pwd):/code -w /code node:6 yarn run encore dev
  else
    echo "Running $@"
    docker run --rm --name node_yarn -v $(pwd):/code -w /code node:6 "$@"
fi