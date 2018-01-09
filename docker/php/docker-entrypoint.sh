#!/bin/bash

setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX /criptim/var
setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX /criptim/var

exec "$@"