FROM node:18-alpine

WORKDIR /app

# Install dependencies during build, and execute Webpack Dev server
CMD npm install && npm run serve -- --host 0.0.0.0 --port 3000
