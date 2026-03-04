#!/bin/bash
source "$HOME/.nvm/nvm.sh"
exec npm --prefix "$(dirname "$0")/../forms/registration" run dev
