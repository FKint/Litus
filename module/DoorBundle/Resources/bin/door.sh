#!/bin/bash

# A very small wrapper around our door script
#

SCRIPT_DIRECTORY=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
cd "${SCRIPT_DIRECTORY}/../../"

python bin/DoorBundle/door.py