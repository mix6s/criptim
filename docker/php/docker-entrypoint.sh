#!/bin/bash

setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX /criptim
setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX /criptim

exec "$@"