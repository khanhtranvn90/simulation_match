FROM node:22.12.0-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . /app

RUN npm run build
RUN npm install -g serve

EXPOSE 5173

# CMD ["serve", "-s", "dist", "-l", "5173"]

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]