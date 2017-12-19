#!/bin/bash
while [ true ]; do 
  if [ $(expr $(date +%s) % 60) -eq 0 ]; then 
    echo "top o da minute";
    curl http://nammabagalkot.in/cryptboard/createData.php
  fi; 
  sleep 5; 
done
