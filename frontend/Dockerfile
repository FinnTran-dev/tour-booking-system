FROM node:18-alpine

# Set working directory
WORKDIR /app

# The node modules are mounted via volumes in docker-compose for dev mode ease.
# Expose port and start
EXPOSE 3000

# Install dependencies if not present and start Webpack dev server
CMD sh -c "if [ ! -d node_modules ]; then npm install; fi && npm run serve -- --host 0.0.0.0 --port 3000"
