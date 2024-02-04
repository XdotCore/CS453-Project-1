docker kill $(docker ps -a -q)
docker rm $(docker ps -a -q)
docker build -t="cs453project1" .
docker run -d -p 8080:80 cs453project1
