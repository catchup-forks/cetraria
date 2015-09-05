#!/usr/bin/env bash
# +-----------------------------------------------------------------------+
# | Cetraria                                                              |
# +-----------------------------------------------------------------------+
# | Copyright (c) 2015 Serghei Iakovlev                                   |
# +-----------------------------------------------------------------------+
# | This source file is subject to the New BSD License that is bundled    |
# | with this package in the file docs/LICENSE.txt.                       |
# |                                                                       |
# | If you did not receive a copy of the license and are unable to        |
# | obtain it through the world-wide-web, please send an email            |
# | to me@klay.me so I can send you a copy immediately.                   |
# +-----------------------------------------------------------------------+

BASE_DIR=$(cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd)
CACHE="annotations data metadata volt"

for i in ${CACHE}; do
    [ ! -z "$(ls ${BASE_DIR}/var/cache/${i}/)" ] && rm -f "${BASE_DIR}/var/cache/${i}"/*
done
wait
