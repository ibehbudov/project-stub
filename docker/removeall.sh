docker stop $(docker ps -a -q) &&
docker rm $(docker ps -a -q) &&
docker rm -f $(docker ps -qa) &&
docker rmi -f $(docker images -aq) &&
docker system prune &&
docker system prune -a