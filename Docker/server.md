```bash
apt update
apt full-upgrade -y
reboot
```

```bash
docker pull kotleta4/based-dot-meme:alpha-alpine (~220M) (~1000M)
docker pull kotleta4/based-dot-meme:alpha-debian (~260M) (~1250M)
```

```bash
apt clean
rm -rf /var/lib/apt/lists/*
```

```bash
docker run -d -p 80:80 -p 443:443 kotleta4/based-dot-meme:alpha-alpine (~90M)
docker run -d -p 80:80 -p 443:443 kotleta4/based-dot-meme:alpha-debian (~100M)
```

```bash
docker stop $(docker ps -a -q)
```

```bash
htop
```