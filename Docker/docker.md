setx /M DOCKER_BUILDKIT 1
docker build --no-cache --build-arg ADMIN_PASSWORD="password123" -t based-dot-meme .
docker run -d -p 80:80 -p 443:443 --name based-meme based-dot-meme

docker tag based-dot-meme kotleta4/based-dot-meme:alpha-alpine
docker push kotleta4/based-dot-meme:alpha-alpine

docker tag based-dot-meme kotleta4/based-dot-meme:alpha-debian
docker push kotleta4/based-dot-meme:alpha-debian

http://localhost
http://localhost/phpmyadmin