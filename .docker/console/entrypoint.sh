#!/bin/bash

apt update && apt install -y mc patch

cat /tmp/.bashrc > /root/.bashrc

bash
