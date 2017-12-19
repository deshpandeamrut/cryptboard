#!/bin/bash
while [ true ]; do 
  if [ $(expr $(date +%s) % 10) -eq 0 ]; then 
    echo "top o da minute";
    curl http://nammabagalkot.in/cryptboard/writeCallDataToJson.php
  fi; 
  sleep 0.1; 
done
