@echo off
docker load -i fnpdocimage.tar
docker run -d -p 8080:80 --name fnpimagecontainer fnpdocimage:latest
echo "FNP Docker container is running on port 8080"
start http://localhost:8080
echo Done! Your app is running
pause