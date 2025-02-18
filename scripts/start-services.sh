#!/bin/bash

# Start the frontend service
cd frontend
node server.js &

# Start the backend service
cd ..
./openagents &

# Wait for any process to exit
wait -n

# Exit with status of process that exited first
exit $?