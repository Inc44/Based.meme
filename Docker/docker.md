setx /M DOCKER_BUILDKIT 1
docker build --no-cache --build-arg ADMIN_PASSWORD="password123" -t based-dot-meme .
docker run -d -p 80:80 -p 443:443 --name based-meme based-dot-meme

http://localhost
http://localhost/phpmyadmin