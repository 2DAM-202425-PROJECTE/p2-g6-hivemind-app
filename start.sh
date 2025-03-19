#!/bin/bash

# Variables
RESET_DB=false
RUN_MIGRATIONS=false
RUN_SEEDERS=false
SKIP_WORKERS=false
ONLY_BACKEND=false
ONLY_FRONTEND=false
HELP=false

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to show help
show_help() {
  echo -e "${BLUE}Usage: ./start.sh [options]${NC}"
  echo -e "\nOptions:"
  echo -e "  --reset              Reset database"
  echo -e "  --migrations         Run migrations"
  echo -e "  --seeders            Run seeders"
  echo -e "  --skip-workers       Do not start workers"
  echo -e "  --only-backend       Start only the backend"
  echo -e "  --only-frontend      Start only the frontend"
  echo -e "  --help               Show this help message and exit"
  exit 0
}

# Process arguments
while [[ "$#" -gt 0 ]]; do
  case "$1" in
    --reset) RESET_DB=true ;; 
    --migrations) RUN_MIGRATIONS=true ;; 
    --seeders) RUN_SEEDERS=true ;;
    --skip-workers) SKIP_WORKERS=true ;; 
    --only-backend) ONLY_BACKEND=true ;; 
    --only-frontend) ONLY_FRONTEND=true ;;  
    --help) HELP=true ;; 
    *) echo -e "${RED}Unknown option: $1${NC}"; exit 1 ;; 
  esac
  shift 
done

# Show help and exit if --help is specified
if [ "$HELP" = true ]; then
  show_help
fi

# Check and move to the project root directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR" || exit 1

# Verify if backend and frontend directories exist
if [ ! -d "nexxus-frontend" ] || [ ! -d "nexxus-backend" ]; then
  echo -e "${RED}Folders 'nexxus-frontend' or 'nexxus-backend' not found.${NC}"
  exit 1
fi

# Update dependencies
echo -e "${YELLOW}🚀 Updating dependencies...${NC}"
cd nexxus-frontend || exit 1
if [ -f package.json ]; then
  npm install &> /dev/null
else
  echo -e "${RED}package.json not found in nexxus-frontend.${NC}"
  exit 1
fi
cd "$SCRIPT_DIR"

cd nexxus-backend || exit 1
if [ -f composer.json ]; then
  composer update &> /dev/null
else
  echo -e "${RED}composer.json not found in nexxus-backend.${NC}"
  exit 1
fi
cd "$SCRIPT_DIR"

# Check and install PHP extensions (for Debian/Ubuntu)
echo -e "${YELLOW}🔍 Checking required PHP extensions...${NC}"
PHP_EXTENSIONS=("gd" "curl" "json" "mbstring" "openssl" "pdo" "tokenizer" "xml" "pdo_mysql")
for ext in "${PHP_EXTENSIONS[@]}"; do
  if ! php -r "exit(extension_loaded('$ext') ? 0 : 1);" > /dev/null 2>&1; then
    echo -e "${YELLOW}Installing php-$ext...${NC}"
    sudo apt-get update -y &> /dev/null
    sudo apt-get install -y "php-$ext" &> /dev/null
    if [ $? -ne 0 ]; then
      echo -e "${RED}Failed to install php-$ext. Please install it manually.${NC}"
      exit 1
    fi
  fi
done
echo -e "${GREEN}✅ PHP extensions are installed.${NC}"

# Create storage link for Laravel
echo -e "${YELLOW}🔗 Creating storage link...${NC}"
cd nexxus-backend || exit 1
if [ -f artisan ]; then
  # Run storage:link and suppress output, checking the exit code
  php artisan storage:link > /dev/null 2>&1
  if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Storage link created successfully.${NC}"
else
  echo -e "${RED}artisan file not found in nexxus-backend.${NC}"
  exit 1
fi
cd "$SCRIPT_DIR"

# Check for incompatibilities
if [ "$RESET_DB" = true ]; then
  if [ "$RUN_MIGRATIONS" = true ]; then
    echo -e "${YELLOW}🚨 Warning: --reset already includes --migrations, so this option will be ignored.${NC}"
    RUN_MIGRATIONS=false
  fi
  if [ "$RUN_SEEDERS" = true ]; then
    echo -e "${YELLOW}🚨 Warning: --reset already includes --seeders, so this option will be ignored.${NC}"
    RUN_SEEDERS=false
  fi
fi

# Execute database logic if arguments are passed
if [ "$RESET_DB" = true ]; then
  echo -e "${YELLOW}🔄 Resetting the database...${NC}"
  cd nexxus-backend || exit 1
  php artisan migrate:fresh --seed
  cd "$SCRIPT_DIR"
fi

if [ "$RUN_MIGRATIONS" = true ]; then
  echo -e "${YELLOW}📦 Running migrations...${NC}"
  cd nexxus-backend || exit 1
  php artisan migrate
  cd "$SCRIPT_DIR"
fi

if [ "$RUN_SEEDERS" = true ]; then
  echo -e "${YELLOW}🌱 Running seeders...${NC}"
  cd nexxus-backend || exit 1
  php artisan db:seed
  cd "$SCRIPT_DIR"
fi

if [ "$ONLY_FRONTEND" = true ]; then
  if [ "$SKIP_WORKERS" = false ]; then
    echo -e "${YELLOW}🚨 Warning: --skip_workers can't work with --only-frontend, so --skip-workers will be set to true.${NC}"
    SKIP_WORKERS=true
  fi
fi

# Start backend if --only-frontend is not specified
if [ "$ONLY_FRONTEND" = false ]; then
  echo -e "${GREEN}🖥️  Starting backend...${NC}"
  cd nexxus-backend || exit 1
  if [ -f artisan ]; then
    php artisan serve --host=127.0.0.1 --port=8000 &
    BACKEND_PID=$!
  else
    echo -e "${RED}artisan file not found in nexxus-backend.${NC}"
    exit 1
  fi
  cd "$SCRIPT_DIR"
fi

# Start frontend if --only-backend is not specified
if [ "$ONLY_BACKEND" = false ]; then
  echo -e "${GREEN}🌐 Starting frontend...${NC}"
  cd nexxus-frontend || exit 1
  npm run dev -- --port=3000 &
  FRONTEND_PID=$!
  cd "$SCRIPT_DIR"
fi

# Start workers if --skip-workers is not specified
if [ "$SKIP_WORKERS" = false ]; then
  echo -e "${GREEN}🛠️  Starting workers...${NC}"
  cd nexxus-backend || exit 1
  if [ -f artisan ]; then
    php artisan queue:work &  
    WORKER_PID=$!
    php artisan reverb:start &  
    REVERB_PID=$!
  else
    echo -e "${RED}artisan file not found in nexxus-backend.${NC}"
    exit 1
  fi
  cd "$SCRIPT_DIR"
fi

# Capture Ctrl+C to stop processes
trap "
  echo -e '${RED}🛑 Stopping servers...${NC}';
  [ -n "$BACKEND_PID" ] && kill $BACKEND_PID;
  [ -n "$FRONTEND_PID" ] && kill $FRONTEND_PID;
  [ -n "$REVERB_PID" ] && kill $REVERB_PID;
  [ -n "$WORKER_PID" ] && kill $WORKER_PID;
  exit 0;
" SIGINT

wait