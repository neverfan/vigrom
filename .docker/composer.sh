#!/bin/bash
docker run --rm -it --init --network workspace --user 1000 -w /var/www -v ${PWD}:/var/www:delegated composer $@
