FROM node:20-alpine AS development-dependencies-env
WORKDIR /app
COPY package.json pnpm-lock.yaml ./
RUN npm install -g pnpm
RUN pnpm install

FROM node:20-alpine AS build-env
WORKDIR /app
COPY . .
COPY --from=development-dependencies-env /app/node_modules ./node_modules
RUN pnpm run build

FROM node:20-alpine AS runtime
WORKDIR /app
COPY --from=build-env /app/build ./build
COPY --from=build-env /app/public ./public
COPY package.json pnpm-lock.yaml server.js ./
RUN npm install -g pnpm
RUN pnpm install --prod
ENV NODE_ENV=production
EXPOSE 3000
CMD ["node", "server.js"]