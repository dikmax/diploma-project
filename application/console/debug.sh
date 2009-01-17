#!/bin/sh

export QUERY_STRING="start_debug=1&debug_port=10000&debug_host=127.0.0.1&debug_stop=1"
/usr/bin/php -q "$@"
